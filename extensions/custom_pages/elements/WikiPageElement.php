<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\extensions\custom_pages\elements;

use humhub\libs\Html;
use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordElement;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
use humhub\modules\wiki\models\WikiPage;
use Yii;

/**
 * Class to manage content record of the Wiki page
 *
 * @property-read WikiPage|null $record
 */
class WikiPageElement extends BaseContentRecordElement
{
    protected const RECORD_CLASS = WikiPage::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('WikiModule.base', 'Wiki page');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contentId' => Yii::t('WikiModule.base', 'Wiki page content ID'),
        ];
    }

    public function __toString()
    {
        return Html::encode($this->record?->title);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return WikiPageElementVariable::instance($this)->setRecord($this->getRecord());
    }
}
