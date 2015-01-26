<?php

/**
 * This is the model class for table "wiki_page_revision".
 *
 * The followings are the available columns in table 'wiki_page_revision':
 * @property integer $id
 * @property integer $revision
 * @property integer $is_latest
 * @property integer $wiki_page_id
 * @property integer $user_id
 * @property string $content
 */
class WikiPageRevision extends HActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'wiki_page_revision';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('revision, wiki_page_id, user_id', 'required'),
            array('revision, is_latest, wiki_page_id, user_id', 'numerical', 'integerOnly' => true),
            array('content', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, revision, is_latest, wiki_page_id, user_id, content', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'revision' => 'Revision',
            'is_latest' => 'Is Latest',
            'wiki_page_id' => 'Wiki Page',
            'user_id' => 'User',
            'content' => 'Content',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('revision', $this->revision);
        $criteria->compare('is_latest', $this->is_latest);
        $criteria->compare('wiki_page_id', $this->wiki_page_id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('content', $this->content, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return WikiPageRevision the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        $this->is_latest = 1;
        return parent::beforeSave();
    }

    public function afterSave()
    {
        WikiPageRevision::model()->updateAll(array('is_latest' => 0), 'wiki_page_id=:wikiPageId AND id!=:selfId', array(':wikiPageId' => $this->wiki_page_id, ':selfId' => $this->id));
        return parent::afterSave();
    }

}
