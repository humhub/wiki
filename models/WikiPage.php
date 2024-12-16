<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\models;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\wiki\activities\WikiPageEditedActivity;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "wiki_page".
 *
 * The followings are the available columns in table 'wiki_page':
 * @property int $id
 * @property string $title
 * @property int $is_home
 * @property int $admin_only
 * @property int $parent_page_id
 * @property int $sort_order
 * @property int $is_container_menu
 * @property int $container_menu_order
 *
 * @property-read WikiPage|null $categoryPage
 * @property-read WikiPageRevision $latestRevision
 * @property-read bool $isCategory
 */
class WikiPage extends ContentActiveRecord implements Searchable
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_ADMINISTER = 'admin';
    public const SCENARIO_EDIT = 'edit';
    public $moduleId = 'wiki';
    /**
     * @inheritdoc
     */
    public $autoAddToWall = true;

    public $canMove = true;

    /**
     * @inheritdoc
     */
    public $wallEntryClass = "humhub\modules\wiki\widgets\WallEntry";
    /**
     * @inheritdoc
     */
    public $managePermission = AdministerPages::class;
    /**
     * @inheritdoc
     */
    protected $createPermission = CreatePage::class;
    /**
     * @var bool Cached result from $this->getIsCategory()
     */
    protected $_isCategory;

    public static function getHome(ContentContainerActiveRecord $container)
    {
        return static::find()->contentContainer($container)->readable()->where(['is_home' => 1])->one();
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'wiki_page';
    }

    /**
     * Find Wiki Page by parent page ID
     *
     * @param ContentContainerActiveRecord $container
     * @param int|null $parentId
     * @return ActiveQueryContent
     * @throws \yii\base\Exception
     */
    public static function findByParentId(ContentContainerActiveRecord $container, ?int $parentId = null): ActiveQueryContent
    {
        return static::find()
            ->contentContainer($container)
            ->readable()
            ->andWhere(['wiki_page.parent_page_id' => $parentId])
            ->orderBy([
                static::tableName() . '.sort_order' => SORT_ASC,
                static::tableName() . '.title' => SORT_ASC,
            ]);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
            ['parent_page_id', 'validateParentPage'],
            [['is_home', 'admin_only', 'is_container_menu', 'container_menu_order'], 'integer'],
        ];

    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_CREATE] = ['title', 'parent_page_id'];
        $scenarios[static::SCENARIO_EDIT] = ($this->isOwner()) ? ['title', 'parent_page_id'] : [];
        $scenarios[static::SCENARIO_ADMINISTER] = ['title', 'is_home', 'admin_only', 'parent_page_id', 'is_container_menu', 'container_menu_order'];
        return $scenarios;
    }

    public function getLatestRevision()
    {
        return $this->hasOne(WikiPageRevision::class, ['wiki_page_id' => 'id'])->andWhere(['wiki_page_revision.is_latest' => 1]);
    }

    public function getRevisions()
    {
        $query = $this->hasMany(WikiPageRevision::class, ['wiki_page_id' => 'id']);
        $query->addOrderBy(['revision' => SORT_DESC]);
        return $query;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $isSpaceContainer = (isset($this->content->contentContainer->class) && $this->content->contentContainer->class == Space::class);

        return [
            'id' => 'ID',
            'title' => 'Title',
            'is_home' => Yii::t('WikiModule.base', 'Is homepage'),
            'admin_only' => Yii::t('WikiModule.base', 'Protected'),
            'parent_page_id' => Yii::t('WikiModule.base', 'Parent Page'),
            'is_container_menu' => $isSpaceContainer
                ? Yii::t('WikiModule.base', 'Show in Space menu')
                : Yii::t('WikiModule.base', 'Show in Profile menu'),
            'container_menu_order' => $isSpaceContainer
                ? Yii::t('WikiModule.base', 'Sort order in Space menu')
                : Yii::t('WikiModule.base', 'Sort order in Profile menu'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (empty($this->parent_page_id)) {
            $this->parent_page_id = null;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->is_home == 1) {

            $query = self::find()->contentContainer($this->content->container);
            $query->andWhere(['wiki_page.is_home' => 1]);
            $query->andWhere(['!=', 'wiki_page.id', $this->id]);

            foreach ($query->each() as $page) {
                $page->is_home = 0;
                $page->save();
            }
        }

        if (!$insert && !Yii::$app->user->isGuest) {
            WikiPageEditedActivity::instance()->from(Yii::$app->user->getIdentity())->about($this)->create();
        }

        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->sortOnTop();
        }
    }

    /**
     * Make this page ordered on top inside its category
     */
    private function sortOnTop()
    {
        $pages = static::find()
            ->contentContainer($this->content->container)
            ->andWhere(['!=', static::tableName() . '.id', $this->id])
            ->orderBy([
                static::tableName() . '.sort_order' => SORT_ASC,
                static::tableName() . '.title' => SORT_ASC,
            ]);

        if (empty($this->parent_page_id)) {
            $pages->andWhere(['IS', 'parent_page_id', new Expression('NULL')]);
        } else {
            $pages->andWhere(['parent_page_id' => $this->parent_page_id]);
        }

        $sort_order = 1;
        $this->updateAttributes(['sort_order' => $sort_order++]);

        foreach ($pages->each() as $page) {
            /* @var WikiPage $page */
            $page->updateAttributes(['sort_order' => $sort_order++]);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSoftDelete()
    {
        $this->updateChildrenAfterDelete();
        parent::afterSoftDelete();
    }

    private function updateChildrenAfterDelete()
    {
        WikiPage::updateAll(['parent_page_id' => $this->parent_page_id], ['parent_page_id' => $this->id]);
    }

    /**
     * Internal edit logic for wiki pages.
     *
     * @param User|null $user
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\IntegrityException
     */
    public function canEditContent(User $user = null): bool
    {
        if ($this->content->canEdit()) {
            return true;
        }

        // Additional checking when user has no permission "Administer pages"
        return !$this->isNewRecord &&
            !$this->admin_only &&
            $this->content->container->getPermissionManager($user)->can(EditPages::class);
    }

    /**
     * @return WikiPageRevision
     */
    public function createRevision()
    {

        $rev = new WikiPageRevision();
        $rev->user_id = Yii::$app->user->id;
        $rev->revision = time();

        $lastRevision = WikiPageRevision::find()->where(['is_latest' => 1, 'wiki_page_id' => $this->id])->one();
        if ($lastRevision !== null) {
            $rev->content = $lastRevision->content;
        }

        if (!$this->isNewRecord) {
            $rev->wiki_page_id = $this->id;
        }


        return $rev;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        foreach ($this->revisions as $revision) {
            $revision->delete();
        }

        $this->updateChildrenAfterDelete();

        return parent::beforeDelete();
    }

    public function validateParentPage()
    {
        if (empty($this->parent_page_id)) {
            return;
        }

        if (is_array($this->parent_page_id) && isset($this->parent_page_id[0])) {
            $this->parent_page_id = $this->parent_page_id[0];
        }

        $query = static::find();
        $query->contentContainer($this->content->container);
        $query->andWhere(['wiki_page.id' => $this->parent_page_id]);
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'wiki_page.id', $this->id]);
        }

        if ($query->count() != 1) {
            $this->addError('parent_page_id', Yii::t('WikiModule.base', 'Invalid category!'));
            return;
        }

        if ($this->isChildPage($this->parent_page_id)) {
            // Exclude infinite recursion
            $this->addError('parent_page_id', Yii::t('WikiModule.base', 'Child page cannot be used as parent page!'));
        }
    }

    /**
     * Check if the given page is a child page
     *
     * @param int $pageId
     * @return bool
     */
    public function isChildPage($pageId): bool
    {
        if ($this->isNewRecord) {
            return false;
        }

        $page = WikiPage::findOne($pageId);
        if (!$page) {
            return false;
        }

        $parentPage = $page->categoryPage;
        while ($parentPage) {
            if ($parentPage->id == $this->id) {
                return true;
            }
            $parentPage = $parentPage->categoryPage;
        }

        return false;
    }

    public function getContentName()
    {
        return Yii::t('WikiModule.base', "Wiki page");
    }

    public function getContentDescription()
    {
        return $this->title;
    }

    public function getUrl()
    {
        return Url::toWiki($this);
    }

    public function getSearchAttributes()
    {
        $content = "";
        if ($this->latestRevision !== null) {
            $content = $this->latestRevision->content;
        }

        return [
            'title' => $this->title,
            'lastPageContent' => $content,
        ];
    }

    public function getIcon()
    {
        return 'fa-book';
    }

    /**
     * @return ActiveQueryContent
     */
    public function findChildren(): ActiveQueryContent
    {
        return static::find()
            ->andWhere(['parent_page_id' => $this->id])
            ->readable()
            ->orderBy([
                static::tableName() . '.sort_order' => SORT_ASC,
                static::tableName() . '.title' => SORT_ASC,
            ]);
    }

    public function getCategoryPage()
    {
        return $this->hasOne(static::class, ['id' => 'parent_page_id']);
    }

    public function afterMove(ContentContainerActiveRecord $container = null)
    {
        if ($this->isCategory) {
            foreach ($this->findChildren()->each() as $childPage) {
                $childPage->updateAttributes(['parent_page_id' => new Expression('NULL')]);
            }
        }

        $this->updateAttributes(['parent_page_id' => new Expression('NULL')]);
        $this->updateAttributes(['is_home' => 0]);
    }

    public function getIsCategory(): bool
    {
        if (!isset($this->_isCategory)) {
            $this->_isCategory = !$this->isNewRecord && $this->findChildren()->exists();
        }

        return $this->_isCategory;
    }
}
