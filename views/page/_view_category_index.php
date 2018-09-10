<?php

use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */

$icon = $page->is_category ? 'fa-file-word-o' : 'fa-file-text-o';

?>

<?php if ($page->is_category): ?>
    <div class="wiki-sub-pages" style="background-color:<?= $this->theme->variable('background-color-secondary') ?>">
        <ul class="wiki-page-list">
            <?= CategoryListItem::widget([
                'title' => Yii::t('WikiModule.base', 'Pages in this category'),
                'pages' => $page->findChildren()->all(),
                'contentContainer' => $page->content->container,
                'icon' => 'fa-list-ol'
            ])?>
        </ul>
    </div>
<?php else: ?>
    <hr>
<?php endif; ?>
