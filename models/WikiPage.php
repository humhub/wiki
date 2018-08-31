<?php

namespace humhub\modules\wiki\models;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable;
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
 *
 */
class WikiPage extends ContentActiveRecord implements Searchable
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_ADMIN_EDIT = 'admin';
    const SCENARIO_EDIT = 'edit';

    /**
     * @inheritdoc
     */
    public $autoAddToWall = true;

    /**
     * @inheritdoc
     */
    public $wallEntryClass = "humhub\modules\wiki\widgets\WallEntry";

    /**
     * @var array newly attached files
     */
    public $newFiles = [];

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
            [['newFiles'], 'safe'],
            ['title', 'required'],
            ['title', 'string', 'max' => 255],
            ['title', 'validateTitle'],
            ['parent_page_id', 'validateParentPage'],
            [['is_home', 'admin_only', 'is_category'], 'integer']
        ];

    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title', 'newFiles'];
        $scenarios['admin'] = ['title', 'is_home', 'admin_only', 'is_category', 'newFiles', 'parent_page_id'];
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
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'is_home' => Yii::t('WikiModule.base', 'Is homepage'),
            'admin_only' => Yii::t('WikiModule.base', 'Protected'),
            'is_category' => Yii::t('WikiModule.base', 'Is category'),
            'parent_page_id' => Yii::t('WikiModule.base', 'Category')
        );
    }


    public function beforeSave($insert)
    {
        if ($this->is_category || empty($this->parent_page_id)) {
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

        return parent::afterSave($insert, $changedAttributes);
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
        return Yii::t('WikiModule.models_WikiPage', "Wiki page");
    }

    public function getContentDescription()
    {
        return $this->title;
    }

    public function getUrl()
    {
        return $this->content->container->createUrl('//wiki/page/view', array('title' => $this->title));
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

    public function findChildren()
    {
        $query = static::find();
        return $query->andWhere(['parent_page_id' => $this->id])->orderBy('title ASC');
    }

    public function getCategoryPage()
    {
        return $this->hasOne(static::class, ['id' => 'parent_page_id']);
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     */
    public function getCategoryList()
    {
        $categories = [];

        $query = static::find()->contentContainer($this->content->container);
        $query->andWhere(['wiki_page.is_category' => 1]);

        if (!$this->isNewRecord) {
            $query->andWhere(['!=', 'wiki_page.id', $this->id]);
        }

        $categories[] = '';
        foreach ($query->all() as $category) {
            $categories[$category->id] = $category->title;
        }

        return $categories;
    }

}
