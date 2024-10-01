<?php

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\PageListItemTitle;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $pages ActiveQueryContent */
/* @var $page WikiPage */
/* @var $title string */
/* @var $icon string|null */
/* @var $iconPage string|null */
/* @var $iconCategory string|null */
/* @var $category WikiPage */
/* @var $hideTitle bool */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $showNumFoldedSubpages bool */
/* @var $level int */
/* @var $levelIndent int */
/* @var $maxLevel int */
/* @var $displaySubPages bool */
?>
<li class="wiki-category-list-item<?= Helper::isCurrentPage($category) ? ' wiki-list-item-selected' : '' ?>"<?php if ($category) : ?> data-page-id="<?= $category->id ?>"<?php endif; ?>>
    <?php if (!$hideTitle) : ?>
        <?= PageListItemTitle::widget([
            'page' => $category,
            'showDrag' => $showDrag,
            'showAddPage' => $showAddPage,
            'title' => $title,
            'icon' => $icon,
            'level' => $level,
            'levelIndent' => $levelIndent,
            'maxLevel' => $maxLevel
        ]) ?>
    <?php endif; ?>
    <?php if ($displaySubPages) : ?>
    <ul class="wiki-page-list"<?php if ($category && $category->isFolded()) : ?> style="display:none"<?php endif; ?>>
        <?php foreach ($pages->each() as $page) : ?>
            <li class="wiki-category-list-item<?= Helper::isCurrentPage($page) ? ' wiki-list-item-selected' : '' ?>" data-page-id="<?= $page->id ?>">
                <?= PageListItemTitle::widget([
                    'page' => $page,
                    'showDrag' => $showDrag,
                    'showAddPage' => $showAddPage,
                    'showNumFoldedSubpages' => $showNumFoldedSubpages,
                    'level' => $level + 1,
                    'levelIndent' => $levelIndent,
                    'maxLevel' => $maxLevel,
                    'icon' => $page->isCategory ? $iconCategory : $iconPage,
                ]) ?>
                <?php if ($page->isCategory) : ?>
                    <?= CategoryListView::widget([
                        'contentContainer' => $contentContainer,
                        'parentCategoryId' => $page->id,
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
