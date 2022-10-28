<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
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
    <div class="wiki-page-sidebar col-lg-4 <?= $hideSidebarOnSmallScreen ? 'hidden-md' : 'col-md-12' ?>">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php WikiContent::begin([
                    'cssClass' => 'wiki-page-content',
                    'title' => Yii::t('WikiModule.base', '<strong>Index</strong>')
                        . WikiSearchForm::widget(['contentContainer' => $contentContainer])
                        . Button::info()->icon('fa-plus')->right()->sm()->link(Url::toWikiCreate($contentContainer))->visible($canCreate)
                        . '<div class="clearfix"></div>',
                    'titleIcon' => 'fa-home',
                    'cols' => 12]) ?>

                <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

                <?php WikiContent::end() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8 <?= $hideSidebarOnSmallScreen ? 'col-md-12' : 'hidden-md' ?>">
        <?= $content ?>
    </div>
</div>