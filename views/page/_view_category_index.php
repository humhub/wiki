<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\services\HierarchyListService;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $page WikiPage */
?>
<?php if ($page->isCategory): ?>
    <div class="wiki-sub-pages<?= Helper::isEnterpriseTheme() ? ' hidden-lg' : '' ?>">
        <ul class="wiki-page-list">
            <?= CategoryListItem::widget([
                'service' => $service = new HierarchyListService($page->content->container),
                'title' => Yii::t('WikiModule.base', 'Subpages'),
                'subItems' => $service->getItemsByParentId($page->id),
                'showDrag' => false,
                'showAddPage' => false,
                'showNumFoldedSubpages' => true,
                'contentContainer' => $page->content->container,
                'levelIndent' => 20,
                'maxLevel' => 1,
                'icon' => false,
                'iconPage' => 'file-text-o',
                'iconCategory' => 'folder',
            ]) ?>
        </ul>
    </div>
<?php endif; ?>
