<?php

/**
 * This is the model class for table "wiki_page".
 *
 * The followings are the available columns in table 'wiki_page':
 * @property integer $id
 * @property string $title
 * @property integer $is_home
 * @property integer $admin_only
 */
class WikiPage extends HActiveRecordContent
{

    // Atm not attach wiki pages to wall
    public $autoAddToWall = false;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'wiki_page';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array();

        if ($this->canAdminister() || $this->isNewRecord) {
            $rules[] = array('title', 'required');
            $rules[] = array('title', 'length', 'max' => 255);
            $rules[] = array('title', 'validateTitle');
        }

        if ($this->canAdminister()) {
            $rules[] = array('is_home, admin_only', 'numerical', 'integerOnly' => true);
        }
        return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'latestRevision' => array(self::HAS_ONE, 'WikiPageRevision', 'wiki_page_id',
                'condition' => 'latestRevision.is_latest=1'),
            'revisions' => array(self::HAS_MANY, 'WikiPageRevision', 'wiki_page_id', 'together' => false, 'order' => 'revision DESC'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'is_home' => 'Is Homepage',
            'admin_only' => 'Protected',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return WikiPage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function afterSave()
    {

        // Make sure there are no multiple homepages
        if ($this->is_home == 1) {
            WikiPage::model()->contentContainer($this->content->container)->updateAll(array('is_home' => 0), 'id!=:selfId', array(':selfId' => $this->id));
        }

        return parent::afterSave();
    }

    public function createRevision()
    {

        $rev = new WikiPageRevision();
        $rev->user_id = Yii::app()->user->id;
        $rev->revision = time();

        $lastRevision = WikiPageRevision::model()->findByAttributes(array('is_latest' => 1, 'wiki_page_id' => $this->id));
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

    public function canAdminister()
    {
        if (get_class($this->content->container) == 'Space') {
            return $this->content->container->isAdmin();
        }

        if (get_class($this->content->container) == 'User') {
            return $this->content->container->id == Yii::app()->user->id;
        }

        return false;
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

        $criteria = new CDbCriteria();
        if (!$this->isNewRecord) {
            $criteria->condition = 't.id != :selfId';
            $criteria->params = array(':selfId' => $this->id);
        }

        $page = WikiPage::model()->contentContainer($this->content->container)->findByAttributes(array('title' => $this->title), $criteria);
        if ($page !== null) {
            $this->addError('title', Yii::t('WikiModule.base', 'Page title already in use!'));
        }
    }

    /**
     * Returns a title/text which identifies this IContent.
     *
     * e.g. Wiki: Page title...
     *
     * @return String
     */
    public function getContentTitle()
    {
        return Yii::t('WikiModule.models_WikiPage', "Wiki page") . " \"" . Helpers::truncateText($this->title, 25) . "\"";
    }

    public function getUrl()
    {
        return $this->content->container->createUrl('//wiki/page/view', array('title' => $this->title));
    }

}
