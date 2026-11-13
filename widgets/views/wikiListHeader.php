<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiSearchForm;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\Icon;

/* @var $icon string */
/* @var $title string */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */
?>
<div class="wiki-page-content-header clearfix">
    <h3><?= ($icon ? Icon::get($icon) : '') . $title ?></h3>
    <?= WikiSearchForm::widget(['contentContainer' => $contentContainer, 'cssClass' => Helper::isEnterpriseTheme() ? 'd-lg-none' : '']) ?>
    <div class="wiki-page-content-header-actions">
        <?= Button::accent(Yii::t('WikiModule.base', 'Index'))->icon('home')
            ->link(Url::toHome($contentContainer))->sm()->cssClass(Helper::isEnterpriseTheme() ? 'd-lg-none' : '') ?>
        <?= Button::accent(Yii::t('WikiModule.base', 'Create page'))->icon('plus')
            ->link(Url::toWikiCreate($contentContainer))->visible($canCreate)->sm() ?>
    </div>
</div>
