<?php

use humhub\compat\CActiveForm;
use yii\helpers\Html;

humhub\modules\wiki\Assets::register($this);
?>
<div class="panel panel-default">

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">

                <?php if (!$page->isNewRecord) : ?>
                    <h1><?php echo Yii::t('WikiModule.views_page_edit', '<strong>Edit</strong> page'); ?></h1>
                <?php else: ?>
                    <h1><?php echo Yii::t('WikiModule.views_page_edit', '<strong>Create</strong> new page'); ?></h1>
                <?php endif; ?>

                <?php $form = CActiveForm::begin(); ?>

                <?php if ($page->canAdminister() || $page->isNewRecord): ?>
                    <div class="form-group">
                        <?php // echo $form->labelEx($page, 'title');  ?>
                        <?php echo $form->textField($page, 'title', array('class' => 'form-control', 'placeholder' => Yii::t('WikiModule.views_page_edit', 'New page title'))); ?>
                        <?php echo $form->error($page, 'title'); ?>
                    </div>
                <?php else: ?>
                    <?php echo $form->hiddenField($page, 'title'); ?>
                <?php endif; ?>


                <div class="form-group">
                    <?php echo $form->textArea($revision, 'content', array('id' => 'txtWikiPageContent', 'rows' => '15', 'placeholder' => Yii::t('WikiModule.views_page_edit', 'Page content'))); ?>
                    <?php echo humhub\widgets\MarkdownEditor::widget(array('fieldId' => 'txtWikiPageContent', 'previewUrl' => $contentContainer->createUrl('preview-markdown'))); ?>
                    <script>
                        $(document).ready(function () {
                            // Fix MarkdownEditor Url Placeholder, user can also insert wiki page title
                            $('#addLinkTarget').attr("placeholder", "<?php echo Yii::t('WikiModule.views_page_edit', 'Enter a wiki page name or url (e.g. http://example.com)'); ?>");
                        });
                    </script>
                </div>

                <?php if ($page->canAdminister()): ?>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <?php echo $form->checkBox($page, 'is_home', array()); ?> <?php echo $page->getAttributeLabel('is_home'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <?php echo $form->checkBox($page, 'admin_only', array()); ?> <?php echo $page->getAttributeLabel('admin_only'); ?>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
                <hr>
                <?php echo Html::submitButton(Yii::t('WikiModule.views_page_edit', 'Save'), array('class' => 'btn btn-primary')); ?>
                <?php CActiveForm::end(); ?>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">
                <?php if (!$page->isNewRecord): ?>

                    <ul class="nav nav-pills nav-stacked">
                        <?php if ($page->canAdminister()): ?>
                            <!-- load modal confirm widget -->
                            <li><?php
                            echo \humhub\widgets\ModalConfirm::widget(array(
                                'uniqueID' => 'modal_pagedelete_' . $page->id,
                                'linkOutput' => 'a',
                                'title' => Yii::t('WikiModule.base', '<strong>Confirm</strong> page deleting'),
                                'message' => Yii::t('WikiModule.base', 'Do you really want to delete this page?'),
                                'buttonTrue' => Yii::t('WikiModule.base', 'Delete'),
                                'buttonFalse' => Yii::t('WikiModule.base', 'Cancel'),
                                'linkContent' => '<i class="fa fa-trash-o delete"></i> ' . Yii::t('WikiModule.base', 'Delete'),
                                'linkHref' => $contentContainer->createUrl('//wiki/page/delete', array('id' => $page->id)),
                                'confirmJS' => 'function(jsonResp) { window.location.href = "' . $contentContainer->createUrl('index') . '"; }'
                            ));
                            ?></li>

                        <?php endif; ?>

                        <li><?php echo Html::a('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Cancel'), $contentContainer->createUrl('//wiki/page/view', array('title' => $page->title))); ?></li>
                        <li class="nav-divider"></li>
                        <?php if ($homePage !== null) : ?>
                            <li><?php echo Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('//wiki/page/index', array())); ?></li>
                        <?php endif; ?>
                        <li><?php echo Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $contentContainer->createUrl('//wiki/page/list', array())); ?></li>
                    </ul>

                <?php else: ?>
                    <ul class="nav nav-pills nav-stacked">
                        <li><?php echo Html::a('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Cancel'), $contentContainer->createUrl('//wiki/page/list', array('title' => $page->title))); ?></li>
                        <li class="nav-divider"></li>
                            <?php if ($homePage !== null) : ?>
                            <li><?php echo Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('//wiki/page/index', array())); ?></li>
                        <?php endif; ?>
                        <li><?php echo Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $contentContainer->createUrl('//wiki/page/list', array())); ?></li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>