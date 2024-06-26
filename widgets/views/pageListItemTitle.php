<?php

use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $page WikiPage */
/* @var $icon string */
/* @var $title string */
/* @var $titleIcon Icon|string|null */
/* @var $titleInfo string */
/* @var $url string */
/* @var $showDrag bool */
/* @var $showAddPage bool */
/* @var $options array */
?>
<?= Html::beginTag('div', $options) ?>
    <div>
        <?= Button::asLink()->icon('arrows')->cssClass('wiki-page-control drag-icon')->visible($page && $showDrag) ?>
        <?= ($icon ? Icon::get($icon) . ' ' : '') . Html::tag($url ? 'a' : 'span', Html::encode($title), ['href' => $url, 'class' => 'page-title-text']) ?>
        <?php if ($titleInfo) : ?>
            <span class="page-title-info"><?= $titleInfo ?></span>
        <?php endif; ?>
    </div>
    <?php if ($titleIcon) : ?>
        <span class="page-title-icon"><?= Icon::get($titleIcon) ?></span>
    <?php endif; ?>
    <?php if ($page && $showAddPage) : ?>
        <?= Button::asLink(null, Url::toWikiCreateForCategory($page))->icon('fa-plus')
            ->cssClass('wiki-page-control tt wiki-category-add')
            ->title(Yii::t('WikiModule.base', 'Add Page')) ?>
    <?php endif; ?>
<?= Html::endTag('div') ?>
