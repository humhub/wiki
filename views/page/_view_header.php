<?php

use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiPath;
use humhub\widgets\Label;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;

/* @var $page WikiPage */
/* @var $revision WikiPageRevision */
/* @var $buttons array|string */

if (empty($buttons)) {
    $buttons = WikiMenu::LINK_EDIT;
}
?>

<div class="wiki-headline">
    <?= WikiPath::widget(['page' => $page]) ?>
    <?= WikiMenu::widget(['page' => $page, 'buttons' => $buttons, 'revision' => $revision ?? null]) ?>

    <div class="wiki-page-title"><?= Html::encode($page->title) ?></div>

    <?= Icon::get('print', ['htmlOptions' => [
            'title' => Yii::t('WikiModule.base', 'Print this wiki page'),
            'data-action-click' => 'wiki.Page.print',
            'class' => 'wiki-icon-print'
        ]]) ?>

    <div class="wiki-content-info clearfix">
        <small>
            <?= Yii::t('WikiModule.base', 'Created by {author}', ['author' => Html::containerLink($page->content->createdBy)]) . ', ' ?>
            <?= Yii::t('WikiModule.base', 'last update {dateTime}', ['dateTime' => TimeAgo::widget(['timestamp' => $page->content->updated_at])]) ?>
            <?= Link::to('(' . Yii::t('WikiModule.base', 'History') . ')', Url::toWikiHistory($page)) ?>
        </small>

        <?php if ($page->is_home) : ?>
            <?= Label::success()->icon('fa-home')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Home')) ?>
        <?php endif; ?>

        <?php if ($page->content->isPublic()) : ?>
            <?= Label::info()->tooltip(Yii::t('ContentModule.widgets_views_label', 'Public'))->icon('fa-globe') ?>
        <?php endif; ?>

        <?php if ($page->admin_only) : ?>
            <?= Label::defaultType()->icon('fa-lock')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Protected')) ?>
        <?php endif; ?>
    </div>
</div>
