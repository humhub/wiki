<?php

namespace humhub\modules\wiki\models;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\space\models\Space;
use humhub\modules\wiki\activities\WikiPageEditedActivity;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\EditPages;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "wiki_page".
 *
 * The followings are the available columns in table 'wiki_page':
 * @property integer $id
 * @property string $title
 * @property integer $is_home
 * @property integer $admin_only
 * @property integer $is_category
 * @property integer $parent_page_id
 * @property integer $sort_order
 * @property integer $is_container_menu
 * @property integer $container_menu_order
 *
 * @property-read WikiPage|null $categoryPage
 * @property-read WikiPageRevision $latestRevision
 *
 */
class WikiPage extends ContentActiveRecord implements Searchable
{
    public $moduleId = 'wiki';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_ADMINISTER = 'admin';
    const SCENARIO_EDIT = 'edit';

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
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'wiki_page';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
            ['title', 'validateTitle'],
            ['parent_page_id', 'validateParentPage'],
            [['is_home', 'admin_only', 'is_category', 'is_container_menu', 'container_menu_order'], 'integer']
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
        $scenarios[static::SCENARIO_ADMINISTER] = ['title', 'is_home', 'admin_only', 'is_category', 'parent_page_id', 'is_container_menu', 'container_menu_order'];
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
            'is_category' => Yii::t('WikiModule.base', 'Is category'),
            'parent_page_id' => Yii::t('WikiModule.base', 'Category'),
            'is_container_menu' => $isSpaceContainer
                ? Yii::t('WikiModule.base', 'Show in Space menu')
                : Yii::t('WikiModule.base', 'Show in Profile menu'),
            'container_menu_order' => $isSpaceContainer
                ? Yii::t('WikiModule.base', 'Sort order in Space menu')
                : Yii::t('WikiModule.base', 'Sort order in Profile menu'),
        ];
    }


    public function beforeSave($insert)
    {
        if (empty($this->parent_page_id)) {
            $this->parent_page_id = null;
        }

        // Check if category flag was removed
        if ((int) $this->getOldAttribute('is_category') != (int) $this->is_category && (int) $this->is_category == 0) {
            WikiPage::updateAll(['parent_page_id' => new Expression('NULL')], ['parent_page_id' => $this->id]);
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->is_home == 1) {

            $query = self::find()->contentContainer($this->content->container);
            $query->andWhere(['wiki_page.is_home' => 1]);
            $query->andWhere(['!=', 'wiki_page.id', $this->id]);

            foreach ($query->all() as $page) {
                $page->is_home = 0;
                $page->save();
            }
        }

        if (!$insert) {
            WikiPageEditedActivity::instance()->from(Yii::$app->user->getIdentity())->about($this)->create();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Internal edit logic for wiki pages.
     *
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\IntegrityException
     */
    public function canEditWikiPage()
    {
        // Can edit
        return $this->content->canEdit() || (!$this->admin_only &&  $this->content->container->can(EditPages::class));
    }

    /**
     * @return WikiPageRevision
     */
    public function createRevision()
    {

        $rev = new WikiPageRevision();
        $rev->user_id = Yii::$app->user->id;
        $rev->revision = time();

        $lastRevision = WikiPageRevision::find()->where(array('is_latest' => 1, 'wiki_page_id' => $this->id))->one();
        if ($lastRevision !== null) {
            $rev->content = $lastRevision->content;
        }

        if (!$this->isNewRecord) {
            $rev->wiki_page_id = $this->id;
        }


        return $rev;
    }

    public function beforeDelete()
    {
        foreach ($this->revisions as $revision) {
            $revision->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * Title field validator
     *
     * @param string $attribute
     * @param array $params
     * @throws \yii\base\Exception
     */
    public function validateTitle($attribute, $params)
    {

        if (strpos($this->title, "/") !== false || strpos($this->title, ")") !== false || strpos($this->title, "(") !== false) {
            $this->addError('title', Yii::t('WikiModule.base', 'Invalid character in page title!'));
        }

        $query = self::find()->contentContainer($this->content->container);
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'wiki_page.id', $this->id]);
        }
        $query->andWhere(['wiki_page.title' => $this->title]);

        if ($query->count() != 0) {
            $this->addError('title', Yii::t('WikiModule.base', 'Page title already in use!'));
        }
    }

    public function validateParentPage($attribute, $params)
    {
        if (empty($this->parent_page_id)) {
            return;
        }

        $query = static::find();
        $query->contentContainer($this->content->container);
        $query->andWhere(['wiki_page.id' => $this->parent_page_id]);
        $query->andWhere(['is_category' => 1]);
        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'wiki_page.id', $this->id]);
        }

        if ($query->count() != 1) {
            $this->addError('parent_page_id', Yii::t('WikiModule.base', 'Invalid category!'));
        }
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

    // Searchable Attributes / Informations
    public function getSearchAttributes()
    {
        $content = "";
        if ($this->latestRevision !== null) {
            $content = $this->latestRevision->content;
        }

        return array(
            'title' => $this->title,
            'lastPageContent' => $content,
        );
    }

    public function getIcon()
    {
        return 'fa-file-word-o';
    }

    public static function getHome(ContentContainerActiveRecord $container)
    {
        return static::find()->contentContainer($container)->readable()->where(['is_home' => 1])->one();
    }

    /**
     * @return \humhub\modules\content\components\ActiveQueryContent
     */
    public function findChildren()
    {
        return static::find()->andWhere(['parent_page_id' => $this->id])->readable()->orderBy('sort_order ASC, title ASC');
    }

    /**
     * @param ContentContainerActiveRecord $contentContainer
     * @return \humhub\modules\content\components\ActiveQueryContent
     * @throws \yii\base\Exception
     */
    public static function findUnsorted(ContentContainerActiveRecord $contentContainer)
    {
        return static::find()->contentContainer($contentContainer)
            ->andWhere(['IS', 'parent_page_id', new Expression('NULL')])
            ->andWhere(['wiki_page.is_category' => 0])
            ->readable()->orderBy('sort_order ASC, title ASC');
    }

    public function getCategoryPage()
    {
        return $this->hasOne(static::class, ['id' => 'parent_page_id']);
    }

    /**
     * @param ContentContainerActiveRecord $container
     * @return ActiveQueryContent
     * @throws \yii\base\Exception
     */
    public static function findCategories(ContentContainerActiveRecord $container)
    {
        return static::find()->contentContainer($container)->andWhere(['wiki_page.is_category' => 1])->orderBy('sort_order ASC, title ASC');
    }

    /**
     * @param ContentContainerActiveRecord $container
     * @param int $categoryId
     * @return ActiveQueryContent
     * @throws \yii\base\Exception
     */
    public static function findByCategoryId(ContentContainerActiveRecord $container, int $categoryId)
    {
        return static::find()
            ->contentContainer($container)
            ->andWhere(['wiki_page.parent_page_id' => $categoryId])
            ->orderBy('sort_order ASC, title ASC');
    }

    public function afterMove(ContentContainerActiveRecord $container = null) {

        if($this->is_category) {
            foreach ($this->findChildren()->all() as $childPage) {
                $childPage->updateAttributes(['parent_page_id' => new Expression('NULL')]);
            }
        }

        $this->updateAttributes(['parent_page_id' => new Expression('NULL')]);
        $this->updateAttributes(['is_home' => 0]);
    }

    public function isFolded(): bool
    {
        if (!$this->is_category) {
            return false;
        }

        if (Yii::$app->user->isGuest) {
            return false;
        }

        return (bool)Yii::$app->user->getIdentity()->getSettings()->get('wiki.foldedCategory.' . $this->id);
    }
}
