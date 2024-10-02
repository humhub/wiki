<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\HierarchyItem;
use humhub\modules\wiki\services\HierarchyListService;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\PageListItemTitle;

/* @var $service HierarchyListService */
/* @var $item HierarchyItem */
/* @var $subItems HierarchyItem[] */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $title string */
/* @var $icon string|null */
/* @var $iconPage string|null */
/* @var $iconCategory string|null */
/* @var $hideTitle bool */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $showNumFoldedSubpages bool */
/* @var $level int */
/* @var $levelIndent int */
/* @var $maxLevel int */
/* @var $displaySubPages bool */
?>
<li class="wiki-category-list-item<?= $service->isCurrentItem($item) ? ' wiki-list-item-selected' : '' ?>"<?php if ($item) : ?> data-page-id="<?= $item->id ?>"<?php endif; ?>>
    <?php if (!$hideTitle) : ?>
        <?= PageListItemTitle::widget([
            'service' => $service,
            'item' => $item,
            'showDrag' => $showDrag,
            'showAddPage' => $showAddPage,
            'title' => $title,
            'icon' => $icon,
            'level' => $level,
            'levelIndent' => $levelIndent,
            'maxLevel' => $maxLevel,
        ]) ?>
    <?php endif; ?>
    <?php if ($displaySubPages) : ?>
    <ul class="wiki-page-list"<?php if ($item && $item->isFolded) : ?> style="display:none"<?php endif; ?>>
        <?php foreach ($subItems as $item) : ?>
            <li class="wiki-category-list-item<?= $service->isCurrentItem($item) ? ' wiki-list-item-selected' : '' ?>" data-page-id="<?= $item->id ?>">
                <?= PageListItemTitle::widget([
                    'service' => $service,
                    'item' => $item,
                    'showDrag' => $showDrag,
                    'showAddPage' => $showAddPage,
                    'showNumFoldedSubpages' => $showNumFoldedSubpages,
                    'level' => $level + 1,
                    'levelIndent' => $levelIndent,
                    'maxLevel' => $maxLevel,
                    'icon' => $item->isCategory ? $iconCategory : $iconPage,
                ]) ?>
                <?php if ($item->isCategory) : ?>
                    <?= CategoryListView::widget([
                        'service' => $service,
                        'contentContainer' => $contentContainer,
                        'parentId' => $item->id,
                        'showDrag' => $showDrag,
                        'showAddPage' => $showAddPage,
                        'jsWidget' => '',
                        'id' => '',
                        'level' => $level + 2,
                        'levelIndent' => $levelIndent,
                        'maxLevel' => $maxLevel,
                    ]) ?>
                <?php else : ?>
                    <ul class="wiki-page-list"></ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</li>
