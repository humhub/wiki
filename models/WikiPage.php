<?php

namespace humhub\modules\wiki\models;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\search\interfaces\Searchable;

/**
 * This is the model class for table "wiki_page".
 *
 * The followings are the available columns in table 'wiki_page':
 * @property integer $id
 * @property string $title
 * @property integer $is_home
 * @property integer $admin_only
 */
class WikiPage extends ContentActiveRecord implements Searchable
{

    /**
     * @inheritdoc
     */
    public $autoAddToWall = true;

    /**
     * @inheritdoc
     */
    public $wallEntryClass = "humhub\modules\wiki\widgets\WallEntry";

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
        $rules = array();
        $rules[] = ['title', 'required'];
        $rules[] = ['title', 'string', 'max' => 255];
        $rules[] = ['title', 'validateTitle'];
        $rules[] = [['is_home', 'admin_only'], 'integer'];
        return $rules;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title'];
        $scenarios['admin'] = ['title', 'is_home', 'admin_only'];
        return $scenarios;
    }

    public function getLatestRevision()
    {
        return $this->hasOne(WikiPageRevision::className(), ['wiki_page_id' => 'id'])->andWhere(['wiki_page_revision.is_latest' => 1]);
    }

    public function getRevisions()
    {
        $query = $this->hasMany(WikiPageRevision::className(), ['wiki_page_id' => 'id']);
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
        );
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->is_home == 1) {

            $query = self::find()->contentContainer($this->content->container)->where(['wiki_page.is_home' => 1])->andWhere(['!=', 'wiki_page.id', $this->id]);
            foreach ($query->all() as $page) {
                $page->is_home = 0;
                $page->save();
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

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
     * @param type $attribute
     * @param type $params
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

}
