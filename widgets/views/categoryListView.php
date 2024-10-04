<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\HierarchyItem;
use humhub\modules\wiki\services\HierarchyListService;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $service HierarchyListService */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $items HierarchyItem[] */
/* @var $options [] */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $level int */
/* @var $levelIndent int */
/* @var $maxLevel int */
?>
<?= Html::beginTag('ul', $options) ?>
    <?php foreach ($items as $item) : ?>
        <?= CategoryListItem::widget([
            'service' => $service,
            'item' => $item,
            'contentContainer' => $contentContainer,
            'showDrag' => $showDrag,
            'showAddPage' => $showAddPage,
            'level' => $level,
            'levelIndent' => $levelIndent,
            'maxLevel' => $maxLevel,
        ]) ?>
    <?php endforeach; ?>
<?= Html::endTag('ul') ?>
