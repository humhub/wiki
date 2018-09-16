<?php
use humhub\libs\Html;
use humhub\modules\wiki\widgets\CategoryListItem;

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $categories \humhub\modules\wiki\models\WikiPage[] */
/* @var $unsortedPages \humhub\modules\wiki\models\WikiPage[] */
/* @var $options [] */
?>
<?= Html::beginTag('ul', $options) ?>

    <?php foreach ($categories as $category): ?>
        <?= CategoryListItem::widget(['category' => $category, 'contentContainer' => $contentContainer])?>
    <?php endforeach; ?>


    <?php if(count($unsortedPages)) : ?>
        <?= CategoryListItem::widget([
            'title' => Yii::t('WikiModule.base', 'Pages without category'),
            'hideTitle' => empty($categories),
            'pages' => $unsortedPages,
            'contentContainer' => $contentContainer
        ]) ?>
    <?php endif; ?>

<?= Html::endTag('ul') ?>


