<?php

use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiPath;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;
use humhub\widgets\Button;

/* @var $page WikiPage */
/* @var $revision WikiPageRevision */
/* @var $buttons array|string */
/* @var $displayTitle bool */

if (empty($buttons)) {
    $buttons = WikiMenu::LINK_EDIT;
}

// Get the current module
$module = Yii::$app->getModule('wiki');                      
// Get the current user
$user = Yii::$app->user->identity;
// Retrieve the current numbering state for this user from the settings
$numberingEnabled = $module->settings->contentContainer($user)->get('wikiNumberingEnabled');
?>

<div class="wiki-headline">
    <div class="wiki-headline-top">
        <?= WikiPath::widget(['page' => $page]) ?>
        <div class="toggle-numbering">
            <!-- Add toggle switch with URL-based parameter for numbering-->
            <a href="<?= Url::current(['toggle-numbering']) ?>" class="btn-sm btn btn-info toggle-numbering">
                <?= $numberingEnabled ? Yii::t('WikiModule.base', 'Disable Numbering') : Yii::t('WikiModule.base', 'Enable Numbering') ?>
            </a>
            <?php if ($page->is_appendable) : ?>
                <?= Button::info(Yii::t('WikiModule.base', 'Append Content'))->link(Url::toWikiAppend($page))->loader(false)->sm()->cssclass('append-content'); ?>
            <?php endif; ?>
        </div>
        <?= WikiMenu::widget([
            'object' => $page,
            'buttons' => $buttons,
            'revision' => $revision ?? null
        ]) ?>
    </div>

    <?php if (!isset($displayTitle) || $displayTitle) : ?>
        <div class="wiki-page-title"><?= Html::encode($page->title) ?></div>
    <?php endif; ?>

    <div class="wiki-content-info">
        <small>
            <?= Yii::t('WikiModule.base', 'Created by {author}', ['author' => Html::containerLink($page->content->createdBy)]) . ', ' ?>
            <?= Yii::t('WikiModule.base', 'last update {dateTime}', ['dateTime' => TimeAgo::widget(['timestamp' => $page->content->updated_at])]) ?>
            <?= Link::to('(' . Yii::t('WikiModule.base', 'History') . ')', Url::toWikiHistory($page)) ?>
        </small>

        <?php if ($page->is_home) : ?>
            <?= Icon::get('home')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Home'))->color('success') ?>
        <?php endif; ?>

        <?php if ($page->content->isPublic()) : ?>
            <?= Icon::get('globe')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Public'))->color('info') ?>
        <?php endif; ?>

        <?php if ($page->admin_only) : ?>
            <?= Icon::get('lock')->tooltip(Yii::t('ContentModule.widgets_views_label', 'Protected')) ?>
        <?php endif; ?>
    </div>
</div>
