<?php

use humhub\libs\Html;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $page WikiPage */
/* @var $icon string */
/* @var $title string */
/* @var $titleInfo string */
/* @var $url string */
/* @var $showDrag bool */
/* @var $showAddPage bool */
/* @var $options array */
?>
<?= Html::beginTag('div', $options) ?>
    <div>
        <?= Button::asLink()->icon('arrows')->cssClass('wiki-page-control drag-icon')->visible($page && $showDrag) ?>
        <i class="fa <?= $icon ?>"></i> <?= Html::tag($url ? 'a' : 'span', Html::encode($title), ['href' => $url, 'class' => 'page-title-text']) ?>
        <?php if ($titleInfo) : ?>
            <span class="page-title-info"><?= $titleInfo ?></span>
        <?php endif; ?>
    </div>
    <?php if ($page && $showAddPage) : ?>
        <?= Button::asLink(null, Url::toWikiCreateForCategory($page))->icon('fa-plus')
            ->cssClass('wiki-page-control tt wiki-category-add')
            ->title(Yii::t('WikiModule.base', 'Add Page')) ?>
    <?php endif; ?>
<?= Html::endTag('div') ?>