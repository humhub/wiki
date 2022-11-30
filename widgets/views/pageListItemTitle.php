<?php

use humhub\libs\Html;
use humhub\modules\wiki\helpers\Helper;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $icon string */
/* @var $title string */
/* @var $url string */
/* @var $showDrag bool */
/* @var $showAddPage bool */
/* @var $options array */
?>
<?= Html::beginTag('div', $options) ?>
    <?= Button::asLink()->icon('fa-bars')->cssClass('wiki-page-control drag-icon')->visible($page && $showDrag) ?>
    <i class="fa <?= $icon ?>"></i> <?= Html::a(Html::encode($title), $url, ['class' => 'page-title-text']) ?>

    <?php if ($page && $showAddPage) : ?>
        <?= Button::asLink(null, Url::toWikiCreateForCategory($page))->icon('fa-plus')
            ->cssClass('wiki-page-control tt wiki-category-add')
            ->title(Yii::t('WikiModule.base', 'Add Page')) ?>
    <?php endif; ?>
<?= Html::endTag('div') ?>