<?php

namespace humhub\modules\wiki\models;

use Yii;
use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;

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
class WikiPageRevision extends ActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public static function tableName()
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
        return [
            [['revision', 'wiki_page_id', 'user_id'], 'required'],
            [['revision', 'is_latest', 'wiki_page_id', 'user_id'], 'integer'],
            ['content', 'safe'],
        ];
    }

    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPage()
    {
        return $this->hasOne(User::class, ['id' => 'wiki_page_id']);
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
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->is_latest = 1;
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        WikiPageRevision::updateAll(['is_latest' => 0], 'wiki_page_id=:wikiPageId AND id!=:selfId', [':wikiPageId' => $this->wiki_page_id, ':selfId' => $this->id]);
        Yii::$app->search->update(WikiPage::findOne(['id' => $this->wiki_page_id]));

        return parent::afterSave($insert, $changedAttributes);
    }

}
