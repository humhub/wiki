<?php

use humhub\libs\Html;
use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $categories ActiveQueryContent */
/* @var $category WikiPage */
/* @var $options [] */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $level int */
/* @var $levelIndent int */
/* @var $maxLevel int */
?>
<?= Html::beginTag('ul', $options) ?>
    <?php foreach ($categories->each() as $category) : ?>
        <?= CategoryListItem::widget([
            'category' => $category,
            'contentContainer' => $contentContainer,
            'showDrag' => $showDrag,
            'showAddPage' => $showAddPage,
            'level' => $level,
            'levelIndent' => $levelIndent,
            'maxLevel' => $maxLevel,
        ]) ?>
    <?php endforeach; ?>
<?= Html::endTag('ul') ?>
