<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\models\HierarchyItem;
use humhub\modules\wiki\services\HierarchyListService;
use humhub\widgets\bootstrap\Link;

/* @var $service HierarchyListService */
/* @var $item HierarchyItem */
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
        <?= Link::to()->icon('arrows')->cssClass('wiki-page-control drag-icon')->visible($item && $showDrag) ?>
        <?= ($icon ? Icon::get($icon) . ' ' : '') . Html::tag($url ? 'a' : 'span', Html::encode($title), ['href' => $url, 'class' => 'page-title-text']) ?>
        <?php if ($titleInfo) : ?>
            <span class="page-title-info"><?= $titleInfo ?></span>
        <?php endif; ?>
    </div>
    <?php if ($titleIcon) : ?>
        <span class="page-title-icon"><?= Icon::get($titleIcon) ?></span>
    <?php endif; ?>
    <?php if ($item && $showAddPage) : ?>
        <?= Link::to(null, $service->getNewWikiPageUrl($item))
            ->icon('plus')
            ->cssClass('wiki-page-control wiki-category-add')
            ->tooltip(Yii::t('WikiModule.base', 'Add Page')) ?>
    <?php endif; ?>
<?= Html::endTag('div') ?>
