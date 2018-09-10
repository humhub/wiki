<?php

use humhub\modules\content\widgets\PermaLink;
use humhub\modules\wiki\helpers\Url;
use humhub\widgets\Link;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $revision \humhub\modules\wiki\models\WikiPageRevision */
/* @var $homePage string */
/* @var $canViewHistory bool */
/* @var $canEdit bool */
/* @var $canAdminister bool */
/* @var $canCreatePage bool */

$icon = $page->is_category ? 'fa-file-word-o' : 'fa-file-text-o';

?>

<div class="wiki-menu-fixed">
    <?php // The content.Content component is used for the Permalink to work?>
    <ul class="nav nav-pills nav-stacked" data-action-component="content.Content">

        <?php if ($revision->is_latest): ?>

            <?php if ($canEdit) : ?>
                <li><?= Link::to(Yii::t('WikiModule.base', 'Edit page'), Url::toWikiEdit($page))->icon('fa-pencil-square-o edit')?>
            <?php endif; ?>

            <?php if ($canViewHistory) : ?>
                <li><?= Link::to(Yii::t('WikiModule.base', 'Page History'), Url::toWikiHistory($page))->icon('fa-clock-o history')?>
            <?php endif; ?>
            <?= PermaLink::widget(['content' => $page->content]) ;?>

        <?php else: ?>

            <?php if ($canAdminister) : ?>
                <li>
                    <?= Link::withAction(Yii::t('WikiModule.base', 'Revert this'), 'client.post', Url::toWikiRevertRevision($page, $revision))->icon('fa-history history')->confirm(
                        Yii::t('WikiModule.base', '<strong>Confirm</strong> page reverting'),
                        Yii::t('WikiModule.base', 'Do you really want to revert this page?'),
                        Yii::t('WikiModule.base', 'Revert')
                    ) ?>
                </li>
            <?php endif; ?>
            <li>
                <?= Link::to(Yii::t('WikiModule.base', 'Go back'), Url::toWikiHistory($page))->icon('fa-reply') ?>
            </li>

        <?php endif; ?>

        <li class="nav-divider"></li>

        <?php if($canCreatePage): ?>
            <li>
                <?= Link::to(Yii::t('WikiModule.base', 'New page'), Url::toWikiCreate($page->content->container))->icon('fa-file-text-o new')?>
            </li>
            <li class="nav-divider"></li>
        <?php endif; ?>

        <?php if ($homePage) : ?>
            <li>
                <?= Link::to(Yii::t('WikiModule.base', 'Main page'), Url::toHome($page->content->container))->icon('fa-newspaper-o') ?>
            </li>
        <?php endif; ?>
        <li>
            <?= Link::to(Yii::t('WikiModule.base', 'Index'), Url::toOverview($page->content->container))->icon('fa-list-alt') ?>
        </li>
    </ul>
</div>