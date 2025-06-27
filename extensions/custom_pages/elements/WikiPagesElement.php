<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordsElement;
use humhub\modules\custom_pages\modules\template\elements\BaseElementVariable;
use humhub\modules\wiki\models\WikiPage;
use Yii;

/**
 * Class to manage content records of the elements with Wiki pages
 */
class WikiPagesElement extends BaseContentRecordsElement
{
    public const RECORD_CLASS = WikiPage::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('WikiModule.base', 'Wiki pages');
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new WikiPagesElementVariable($this);
    }
}
