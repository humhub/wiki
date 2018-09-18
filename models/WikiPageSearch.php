<?php


namespace humhub\modules\wiki\models;


use humhub\modules\content\components\ContentContainerActiveRecord;
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
     * @var string label
     */
    public $label;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => Yii::t('WikiModule.base', 'Choose a Wiki Page'),
            'anchor' => Yii::t('WikiModule.base', 'Headline'),
            'label' => Yii::t('WikiModule.base', 'Label')
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