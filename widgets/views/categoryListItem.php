<?php

use humhub\libs\Html;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $pages \humhub\modules\wiki\models\WikiPage[] */
/* @var $title string */
/* @var $icon string */
/* @var $category \humhub\modules\wiki\models\WikiPage|null */
/* @var $url string */
/* @var $hideTitle bool */
/* @var $showAddPage bool */
/* @var $showDrag bool */
/* @var $options [] */
/* @var $context \humhub\modules\wiki\widgets\CategoryListItem */

?>

<li class="wiki-category-list-item" <?= ($category) ? 'data-page-id="' . $category->id . '"' : '' ?>>
    <div class="page-category-title" <?= ($hideTitle) ? 'style="display:none"' : '' ?>>
        <?= Button::asLink()->icon('fa-bars')->cssClass('wiki-page-control drag-icon')->visible($category && $showDrag) ?>
        <i class="fa <?= $icon ?>"></i> <?= Html::a(Html::encode($title), $url) ?>

        <?php if($category) : ?>
            <?= Button::asLink(null, Url::toWikiCreateForCategory($category))->icon('fa-plus')
                ->cssClass('wiki-page-control tt wiki-category-add')->style('display:none')
                ->title(Yii::t('WikiModule.base', 'Add Page'))->visible($showAddPage) ?>
        <?php endif; ?>

    </div>
    <ul class="wiki-page-list">
        <?php foreach ($pages as $page): ?>
            <li data-page-id="<?= $page->id ?>">
                <div class="page-title">
                    <?= Button::asLink()->icon('fa-bars')
                        ->cssClass('wiki-page-control drag-icon')
                        ->visible($showDrag) ?>
                    <i class="fa fa-file-text-o"></i> <?= Html::a(Html::encode($page->title), $page->getUrl()); ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</li>


