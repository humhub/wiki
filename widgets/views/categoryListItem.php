<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\PageListItemTitle;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $pages WikiPage[] */
/* @var $title string */
/* @var $icon string */
/* @var $category WikiPage */
/* @var $hideTitle bool */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $level int */
?>

<li class="wiki-category-list-item"<?php if ($category) : ?> data-page-id="<?= $category->id ?>"<?php endif; ?>>
    <?php if (!$hideTitle) : ?>
        <?= PageListItemTitle::widget([
            'page' => $category,
            'showDrag' => $showDrag,
            'showAddPage' => $showAddPage,
            'title' => $title,
            'icon' => $icon,
            'level' => $level,
        ]) ?>
    <?php endif; ?>
    <ul class="wiki-page-list"<?php if ($category && $category->isFolded()) : ?> style="display:none"<?php endif; ?>>
        <?php foreach ($pages as $page) : ?>
            <li class="wiki-category-list-item" data-page-id="<?= $page->id ?>">
                <?= PageListItemTitle::widget([
                    'page' => $page,
                    'showDrag' => $showDrag,
                    'showAddPage' => $showAddPage,
                    'level' => $level + 1,
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
                    ]) ?>
                <?php else : ?>
                    <ul class="wiki-page-list"></ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</li>