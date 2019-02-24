<?php

use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */

$icon = $page->is_category ? 'fa-file-word-o' : 'fa-file-text-o';

$pages = $page->findChildren()->all();
?>

<?php if ($page->is_category): ?>
    <div class="wiki-sub-pages" style="background-color:<?= $this->theme->variable('background-color-secondary') ?>">
        <ul class="wiki-page-list">
            <?php if(!empty($pages)) : ?>
                <?= CategoryListItem::widget([
                    'title' => Yii::t('WikiModule.base', 'Pages in this category'),
                    'pages' => $pages,
                    'showDrag' => false,
                    'showAddPage' => false,
                    'contentContainer' => $page->content->container,
                    'icon' => 'fa-list-ol'
                ])?>
            <?php else : ?>
                <div class="page-category-title" style="margin-bottom:0">
                    <i class="fa fa-list-ol"></i> <?= Yii::t('WikiModule.base', 'There are no pages in this category') ?>
                </div>
            <?php endif; ?>
        </ul>
    </div>
<?php else: ?>
    <hr>
<?php endif; ?>
