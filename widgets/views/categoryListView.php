<?php

use humhub\libs\Html;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $categories \humhub\modules\wiki\models\WikiPage[] */
/* @var $options [] */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $level int */
?>
<?= Html::beginTag('ul', $options) ?>
    <?php foreach ($categories as $category) : ?>
        <?php if ($category->content->canView()) : ?>
            <?= CategoryListItem::widget([
                'category' => $category,
                'contentContainer' => $contentContainer,
                'showDrag' => $showDrag,
                'showAddPage' => $showAddPage,
                'level' => $level,
            ]) ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?= Html::endTag('ul') ?>