<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\helpers\Url;
use Yii;

class WikiSearchForm extends Widget
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var string
     */
    public $cssClass;

    /**
     * @var string
     */
    public $placeholder;

    public function init()
    {
        parent::init();

        if (!isset($this->placeholder)) {
            $this->placeholder = Yii::t('WikiModule.base', 'Search');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('wikiSearchForm', [
            'placeholder' => $this->placeholder,
            'keyword' => Yii::$app->request->get('keyword'),
            'submitUrl' => Url::toSearch($this->contentContainer),
            'cssClass' => $this->cssClass,
        ]);
    }
}
