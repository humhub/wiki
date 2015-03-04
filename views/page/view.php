<div class="panel panel-default">
    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">
                <h1><strong><?php echo CHtml::encode($page->title); ?></strong></h1>
                <hr>

                <div class="markdown-render">
                    <?php $this->widget('application.widgets.MarkdownViewWidget', array('markdown' => $content, 'parserClass' => 'WikiMarkdownParser')); ?>
                </div>
                <hr>

                <div class="social-controls">
                    <?php $this->widget('application.modules_core.comment.widgets.CommentLinkWidget', array('object' => $page)); ?>
                    &middot; <?php $this->widget('application.modules_core.like.widgets.LikeLinkWidget', array('object' => $page)); ?>
                </div>
                <?php $this->widget('application.modules_core.comment.widgets.CommentsWidget', array('object' => $page)); ?>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">
                <ul class="nav nav-pills nav-stacked">

                    <?php if ($revision->is_latest): ?>

                        <?php if (!$page->admin_only || $page->canAdminister()) : ?>
                            <li><?php echo CHtml::link('<i class="fa fa-pencil-square-o edit"></i> ' . Yii::t('WikiModule.base', 'Edit page'), $this->createContainerUrl('edit', array('id' => $page->id))); ?></li>
                        <?php endif; ?>

                        <li><?php echo CHtml::link('<i class="fa fa-clock-o history"></i> ' . Yii::t('WikiModule.base', 'Page History'), $this->createContainerUrl('//wiki/page/history', array('id' => $page->id))); ?></li>

                    <?php else: ?>

                        <?php if (!$page->admin_only || $page->canAdminister()) : ?>

                            <!-- load modal confirm widget -->
                            <li><?php
                                $this->widget('application.widgets.ModalConfirmWidget', array(
                                    'uniqueID' => 'modal_pagedelete_' . $page->id,
                                    'linkOutput' => 'a',
                                    'title' => Yii::t('WikiModule.base', '<strong>Confirm</strong> page reverting'),
                                    'message' => Yii::t('WikiModule.base', 'Do you really want to revert this page?'),
                                    'buttonTrue' => Yii::t('WikiModule.base', 'Revert'),
                                    'buttonFalse' => Yii::t('WikiModule.base', 'Cancel'),
                                    'linkContent' => '<i class="fa fa-history history"></i> ' . Yii::t('WikiModule.base', 'Revert this'),
                                    'linkHref' => $this->createContainerUrl('revert', array('id' => $page->id, 'toRevision' => $revision->revision)),
                                    'confirmJS' => 'function(jsonResp) { window.location.href = "' . $this->createContainerUrl('view', array('title' => $page->title)) . '"; }'
                                ));
                                ?></li>


                        <?php endif; ?>
                        <li><?php echo CHtml::link('<i class="fa fa-reply"></i> ' . Yii::t('WikiModule.base', 'Go back'), $this->createContainerUrl('history', array('id' => $page->id))); ?></li>

                    <?php endif; ?>


                    <li class="nav-divider"></li>
                    <li><a href="<?php echo $this->createContainerUrl('//wiki/page/edit'); ?>"><i
                                class="fa fa-file-text-o new"></i> <?php echo Yii::t('WikiModule.base', 'New page'); ?>
                        </a></li>

                    <li class="nav-divider"></li>
                        <?php if ($homePage !== null) : ?>
                        <li><?php echo CHtml::link('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $this->createContainerUrl('//wiki/page/index', array())); ?></li>
                    <?php endif; ?>
                    <li><?php echo CHtml::link('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $this->createContainerUrl('//wiki/page/list', array())); ?></li>
                </ul>


            </div>
        </div>


    </div>

</div>
