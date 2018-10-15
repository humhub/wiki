<?php

use humhub\libs\Html;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $pages \humhub\modules\wiki\models\WikiPage[] */
/* @var $title string */
/* @var $icon string */
/* @var $category \humhub\modules\wiki\models\WikiPage|null*/
/* @var $url string */
/* @var $hideTitle bool */
/* @var $editUrl string */
/* @var $options [] */
/* @var $context \humhub\modules\wiki\widgets\CategoryListItem */

$canEditCategory = $category && $editUrl && $this->context->canEdit($category);
?>

<li class="wiki-category-list-item" <?= ($category) ? 'data-page-id='.$category->id.'"' : ''  ?>>
    <div class="page-category-title <?= $category && $this->context->canEdit($category) ? 'editable' : '' ?>" <?= ($hideTitle) ? 'style="display:none"' : '' ?>>
        <i class="fa <?= $icon ?>"></i> <?= Html::a(Html::encode($title) , $url) ?>

        <?php if($canEditCategory) : ?>
            <?= Button::asLink(null, $editUrl)->icon('fa-pencil')
                ->style('display:none')->cssClass('wiki-page-control tt')->right()->title(Yii::t('base', 'Edit'))?>

            <?= Button::asLink(null, Url::toWikiCreateForCategory($category))->icon('fa-plus')->
                style('display:none')->cssClass('wiki-page-control tt')->right()->title(Yii::t('WikiModule.base', 'Add Page')) ?>

            <?= Button::asLink()->icon('fa-arrows')->style('display:none')->cssClass('wiki-page-control drag-icon') ?>
        <?php endif; ?>
    </div>
    <ul class="wiki-page-list">
        <?php foreach ($pages as $page): ?>
            <li data-page-id="<?= $page->id ?>">
                <div class="page-title <?= $this->context->canEdit($page) ? 'editable' : '' ?>">
                    <i class="fa fa-file-text-o"></i> <?= Html::a(Html::encode($page->title), $page->getUrl()); ?>
                    <?= Button::asLink(null, $contentContainer->createUrl('/wiki/page/edit', ['id' => $page->id]))
                        ->icon('fa-pencil')
                        ->style('display:none')
                        ->cssClass('wiki-page-control tt')->visible($this->context->canEdit($page))->right()->title(Yii::t('base', 'Edit')) ?>

                    <?= Button::asLink()->icon('fa-arrows')->style('display:none')->cssClass('wiki-page-control drag-icon')->visible($this->context->canEdit($page)) ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</li>


