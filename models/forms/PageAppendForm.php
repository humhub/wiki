<?php

namespace humhub\modules\wiki\models\forms;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\Module;
use humhub\modules\wiki\widgets\WikiRichText;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class PageAppendForm extends Model
{
    public $content;

    public $page;

    /**
     * @inheritdoc
     */
    public function __construct(WikiPage $page, $config = [])
    {
        $this->page = $page;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [['content', 'string']];
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function save()
    {
        if(!$this->validate()) return false;
        $this->page->appendRevision($this->content);

        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $labels = [
            'content' => Yii::t('WikiModule.base', 'Content'),
        ];

        return $labels;
    }

}
