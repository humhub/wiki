<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this \humhub\components\View */
/* @var $homePage boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $canCreate bool */

humhub\modules\wiki\assets\Assets::register($this);

$homeUrl = $contentContainer->createUrl('/wiki/page/index');
$createUrl = $contentContainer->createUrl('//wiki/page/edit');
?>
<div class="panel panel-default wiki-bg">

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