<?php

use humhub\libs\Helpers;
use humhub\libs\Html;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicLabel;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Label;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */

$icon = $page->is_category ? 'file-word-o' : 'file-text-o';

if ($page->is_home) {
    $icon = 'home';
}
?>

<h1 class="wiki-headline">
    <?= Icon::get($icon) ?>
    <span class="wiki-page-title"><?= Html::encode($page->title) ?></span>

    <?php if ($page->is_home) : ?>
        <?= Label::success()->icon('fa-home')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Home'))->right() ?>
    <?php endif; ?>

    <?php if ($page->content->isPublic()) : ?>
        <?= Label::info()->tooltip(Yii::t('ContentModule.widgets_views_label', 'Public'))->icon('fa-globe')->right() ?>
    <?php endif; ?>

    <?php if ($page->admin_only) : ?>
        <?= Label::defaultType()->icon('fa-lock')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Protected'))->right() ?>
    <?php endif; ?>

    <?php if ($page->categoryPage) : ?>
        <?= Label::primary(Helpers::truncateText($page->categoryPage->title, 30))
            ->withLink(Link::to(null, $page->categoryPage->getUrl()))->right() ?>
    <?php endif; ?>

    <?= Icon::get('print', ['htmlOptions' => [
            'title' => Yii::t('WikiModule.base', 'Print this wiki page'),
            'data-action-click' => 'wiki.Page.print',
            'class' => 'wiki-icon-print'
        ]]) ?>
</h1>

<div class="wiki-content-info clearfix">
    <small>
        <?= trim(Yii::t('WikiModule.base', 'Last updated ')) . ' ' . TimeAgo::widget(['timestamp' => $page->content->updated_at]) ?>

        <?php if ($page->content->updatedBy !== null): ?>
            <?= Yii::t('WikiModule.base', 'by') ?>
            <strong>
                <a href="<?= $page->content->updatedBy->getUrl() ?>" style="color:<?= $this->theme->variable('info') ?>"
                   data-contentcontainer-id="<?= $page->content->updatedBy->contentcontainer_id ?>">
                    <?= Html::encode($page->content->updatedBy->displayName) ?>
                </a>
            </strong>
        <?php endif; ?>

    </small>
</div>

<hr class="wiki-headline-seperator">
