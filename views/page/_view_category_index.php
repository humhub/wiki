<?php

use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $page WikiPage */
?>
<?php if ($page->isCategory): ?>
    <div class="wiki-sub-pages">
        <ul class="wiki-page-list">
            <?= CategoryListItem::widget([
                'title' => $page->title,
                'pages' => $page->findChildren()->all(),
                'showDrag' => false,
                'showAddPage' => false,
                'contentContainer' => $page->content->container,
                'levelIndent' => 20,
            ])?>
        </ul>
    </div>
<?php else: ?>
    <hr>
<?php endif; ?>
