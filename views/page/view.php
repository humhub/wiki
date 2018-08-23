<?php

use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\like\widgets\LikeLink;
use humhub\widgets\MarkdownView;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $revision \humhub\modules\wiki\models\WikiPageRevision */

humhub\modules\wiki\Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">

        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-9 wiki-content">
                <h1>
                    <strong><?= Html::encode($page->title); ?></strong>
                    <?php if ($page->categoryPage !== null): ?>
                        <a href="<?= $page->categoryPage->getUrl(); ?>" class="pull-right"><span
                                    class="label label-primary"><?= $page->categoryPage->title; ?></span></a>
                    <?php endif; ?>
                </h1>
                <hr>

                <div class="markdown-render">
                    <?= MarkdownView::widget(['markdown' => $content, 'parserClass' => "humhub\modules\wiki\Markdown"]); ?>
                </div>

                <?php if (!empty($content)) : ?>
                    <hr>
                <?php endif; ?>

                <?php if ($page->is_category): ?>
                    <h1><?= Yii::t('WikiModule.base', 'Pages in this category'); ?></h1>
                    <ul class="wiki-page-list">
                        <?php foreach ($page->findChildren()->all() as $page): ?>
                            <li><?= Html::a('<i class="fa fa-file-text-o"></i> ' . Html::encode($page->title), $page->getUrl()); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <br/>
                    <hr/>
                <?php endif; ?>
                <div class="social-controls">
                    <?= LikeLink::widget(['object' => $page]); ?>
                    &middot; <?= CommentLink::widget(['object' => $page]); ?>
                </div>
                <?= Comments::widget(['object' => $page]); ?>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-3 wiki-menu">
                <ul class="nav nav-pills nav-stacked">

                    <?php if ($revision->is_latest): ?>

                        <?php if ($this->context->canEdit($page)) : ?>
                            <li><?= Html::a('<i class="fa fa-pencil-square-o edit"></i> ' . Yii::t('WikiModule.base', 'Edit page'), $contentContainer->createUrl('/wiki/page/edit', array('id' => $page->id))); ?></li>
                        <?php endif; ?>

                        <?php if ($canViewHistory) : ?>
                            <li><?= Html::a('<i class="fa fa-clock-o history"></i> ' . Yii::t('WikiModule.base', 'Page History'), $contentContainer->createUrl('/wiki/page/history', array('id' => $page->id))); ?></li>
                        <?php endif; ?>
                        <li>
                            <?php
                            echo humhub\widgets\ModalConfirm::widget(array(
                                'uniqueID' => 'modal_permalink',
                                'linkOutput' => 'a',
                                'title' => Yii::t('ContentModule.widgets_views_permaLink', '<strong>Permalink</strong> to this page'),
                                'message' => '<textarea rows="3" id="permalink-txt" class="form-control permalink-txt">' . \yii\helpers\Url::to(['/content/perma', 'id' => $page->content->id], true) . '</textarea><p class="help-block">Copy to clipboard: Ctrl+C, Enter</p>',
                                'buttonFalse' => Yii::t('ContentModule.widgets_views_permaLink', 'Close'),
                                'linkContent' => '<i class="fa fa-link link"></i> ' . Yii::t('WikiModule.base', 'Permalink'),
                                'linkHref' => '',
                                'confirmJS' => 'function(jsonResp) { wallDelete(jsonResp); }',
                                'modalShownJS' => 'setTimeout(function(){$("#permalink-txt").focus(); $("#permalink-txt").select();}, 1);'
                            ));
                            ?>
                        </li>


                    <?php else: ?>

                        <?php if ($this->context->canAdminister()) : ?>

                            <!-- load modal confirm widget -->
                            <li><?php
                                echo \humhub\widgets\ModalConfirm::widget(array(
                                    'uniqueID' => 'modal_pagedelete_' . $page->id,
                                    'linkOutput' => 'a',
                                    'title' => Yii::t('WikiModule.base', '<strong>Confirm</strong> page reverting'),
                                    'message' => Yii::t('WikiModule.base', 'Do you really want to revert this page?'),
                                    'buttonTrue' => Yii::t('WikiModule.base', 'Revert'),
                                    'buttonFalse' => Yii::t('WikiModule.base', 'Cancel'),
                                    'linkContent' => '<i class="fa fa-history history"></i> ' . Yii::t('WikiModule.base', 'Revert this'),
                                    'linkHref' => $contentContainer->createUrl('/wiki/page/revert', array('id' => $page->id, 'toRevision' => $revision->revision)),
                                    'confirmJS' => 'function(jsonResp) { window.location.href = "' . $contentContainer->createUrl('/wiki/page/view', array('title' => $page->title)) . '"; }'
                                ));
                                ?></li>

                        <?php endif; ?>
                        <li><?= Html::a('<i class="fa fa-reply"></i> ' . Yii::t('WikiModule.base', 'Go back'), $contentContainer->createUrl('/wiki/page/history', array('id' => $page->id))); ?></li>

                    <?php endif; ?>

                    <li class="nav-divider"></li>

                    <?php if ($this->context->canCreatePage()): ?>
                        <li><a href="<?php echo $contentContainer->createUrl('/wiki/page/edit'); ?>"><i
                                        class="fa fa-file-text-o new"></i> <?php echo Yii::t('WikiModule.base', 'New page'); ?>
                            </a></li>
                        <li class="nav-divider"></li>
                    <?php endif; ?>

                    <?php if ($homePage !== null) : ?>
                        <li><?= Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('//wiki/page/index', array())); ?></li>
                    <?php endif; ?>
                    <li><?= Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $contentContainer->createUrl('//wiki/overview/list', array())); ?></li>
                </ul>


            </div>
        </div>


    </div>

</div>
