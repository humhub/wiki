<?php

use humhub\libs\Html;
use humhub\widgets\Button;

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $pages \humhub\modules\wiki\models\WikiPage[] */
/* @var $title string */
/* @var $icon string */
/* @var $category \humhub\modules\wiki\models\WikiPage|null*/
/* @var $url string */
/* @var $editUrl string */
/* @var $canEdit bool */
/* @var $options [] */
/* @var $context \humhub\modules\wiki\widgets\CategoryListItem */
?>

<li class="wiki-category-list-item" data-page-id="<?= ($category) ? $category->id : '' ?>">
    <div class="page-category-title">
        <i class="fa <?= $icon ?>"></i> <?= Html::a(Html::encode($title) , $url) ?>
        <?= Button::asLink(null, $editUrl)->icon('fa-pencil')->style('display:none')->cssClass('wiki-edit')
            ->visible($category && $editUrl && $this->context->canEdit($category)) ?>
    </div>
    <ul class="wiki-page-list">
        <?php foreach ($pages as $page): ?>
            <li data-page-id="<?= $page->id ?>">
                <div class="page-title">
                    <i class="fa fa-file-text-o"></i> <?= Html::a(Html::encode($page->title), $page->getUrl()); ?>
                    <?= Button::asLink(null, $contentContainer->createUrl('/wiki/page/edit', ['id' => $page->id]))
                        ->icon('fa-pencil')->style('display:none')->cssClass('wiki-edit')->visible($this->context->canEdit($page)) ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</li>


