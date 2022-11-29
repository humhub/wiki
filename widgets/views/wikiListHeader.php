<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiSearchForm;
use humhub\widgets\Button;

/* @var $icon string */
/* @var $title string */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */
?>
<div class="wiki-page-content-header">
    <h3><?= ($icon ? Icon::get($icon) : '') . $title ?></h3>
    <?= WikiSearchForm::widget(['contentContainer' => $contentContainer, 'cssClass' => Helper::isEnterpriseTheme() ? 'hidden-lg' : '']) ?>
    <div class="wiki-page-content-header-actions">
        <?= Button::info(Yii::t('WikiModule.base', 'Index'))->icon('fa-home')
            ->link(Url::toHome($contentContainer))->sm()->cssClass(Helper::isEnterpriseTheme() ? 'hidden-lg' : '') ?>
        <?= Button::info(Yii::t('WikiModule.base', 'Create page'))->icon('fa-plus')
            ->link(Url::toWikiCreate($contentContainer))->visible($canCreate)->sm() ?>
    </div>
    <div class="clearfix"></div>
</div>