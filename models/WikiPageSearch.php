<?php


namespace humhub\modules\wiki\models;


use Yii;
use yii\base\Model;

class WikiPageSearch extends Model
{
    /**
     * @var string wiki page search label
     */
    public $title;

    /**
     * @var string wiki page search anchor
     */
    public $anchor;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('WikiModule.base', 'Title'),
            'anchor' => Yii::t('WikiModule.base', 'Anchor')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'anchor'], 'safe']
        ];
    }

}