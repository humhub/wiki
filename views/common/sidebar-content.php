<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiSearchForm;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */
/* @var $content string */
/* @var $hideSidebarOnSmallScreen bool */

Assets::register($this);
?>
<div class="row">
    <div class="wiki-page-sidebar col-lg-4 <?= $hideSidebarOnSmallScreen ? 'visible-lg' : 'col-md-12' ?>">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php WikiContent::begin(['cssClass' => 'wiki-page-content', 'cols' => 12]) ?>
                <div class="wiki-page-content-header">
                    <h3><?= Icon::get('home') ?> <?= Yii::t('WikiModule.base', 'Index') ?></h3>
                    <?= WikiSearchForm::widget(['contentContainer' => $contentContainer]) ?>
                    <div class="wiki-page-content-header-actions">
                        <?= Button::info(Yii::t('WikiModule.base', 'Last edited'))->sm()->link(Url::toLastEdited($contentContainer))->cssClass('hidden-lg') ?>
                        <?= Button::info('<span class="hidden-lg">' . Yii::t('WikiModule.base', 'Create page') . '</span>')->icon('fa-plus')->sm()->link(Url::toWikiCreate($contentContainer))->visible($canCreate)->cssClass('') ?>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

                <?php WikiContent::end() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8 <?= $hideSidebarOnSmallScreen ? 'col-md-12' : 'visible-lg' ?>">
        <?= $content ?>
    </div>
</div>