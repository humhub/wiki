<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\PageListItemTitle;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $pages WikiPage[] */
/* @var $title string */
/* @var $icon string */
/* @var $category WikiPage|null */
/* @var $hideTitle bool */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $level int */
?>

<li<?php if (!$category || $category->isCategory) : ?> class="wiki-category-list-item"<?php endif; ?><?php if ($category) : ?> data-page-id="<?= $category->id ?>"<?php endif; ?>>
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
    <?php if (!empty($pages)) : ?>
    <ul class="wiki-page-list"<?php if ($category && $category->isFolded()) : ?> style="display:none"<?php endif; ?>>
        <?php foreach ($pages as $page): ?>
            <li<?php if ($page->isCategory) : ?> class="wiki-category-list-item"<?php endif; ?> data-page-id="<?= $page->id ?>">
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
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</li>


