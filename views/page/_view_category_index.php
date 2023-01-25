<?php

use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $page WikiPage */
/* @var $hasContent bool */
?>
<?php if ($page->isCategory): ?>
    <div class="wiki-sub-pages<?= $hasContent && Helper::isEnterpriseTheme() ? ' hidden-lg' : '' ?>">
        <ul class="wiki-page-list">
            <?= CategoryListItem::widget([
                'title' => $page->title,
                'pages' => $page->findChildren()->all(),
                'showDrag' => false,
                'showAddPage' => false,
                'showNumFoldedSubpages' => true,
                'contentContainer' => $page->content->container,
                'levelIndent' => 20,
                'maxLevel' => 1,
            ])?>
        </ul>
    </div>
<?php endif; ?>
