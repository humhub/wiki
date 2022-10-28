<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */

Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <?php WikiContent::begin([
                'cssClass' => 'wiki-page-content',
                'title' => Yii::t('WikiModule.base', '<strong>Index</strong>'),
                'titleIcon' => 'fa-list-ol',
                'cols' => 12]) ?>

            <?= Button::success(Yii::t('WikiModule.base', 'New page'))->icon('fa-plus')
                ->right()->sm()->link(Url::toWikiCreate($contentContainer))->visible($canCreate); ?>

            <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

            <?php WikiContent::end() ?>
        </div>
    </div>
</div>