<?php

use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
$pages = $page->findChildren()->all();
?>

<?php if ($page->isCategory): ?>
    <div class="wiki-sub-pages" style="background-color:<?= $this->theme->variable('background-color-secondary') ?>">
        <ul class="wiki-page-list">
            <?= CategoryListItem::widget([
                'title' => Yii::t('WikiModule.base', 'Pages in this category'),
                'pages' => $pages,
                'showDrag' => false,
                'showAddPage' => false,
                'contentContainer' => $page->content->container,
                'icon' => 'fa-list-ol'
            ])?>
        </ul>
    </div>
<?php else: ?>
    <hr>
<?php endif; ?>
