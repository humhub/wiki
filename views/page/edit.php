<div class="panel panel-default">

    <div class="panel-body">

        <div class="row">
            <div class="col-md-10 wiki-content">

                <?php if (!$page->isNewRecord) : ?>
                    <h1><?php echo Yii::t('WikiModule.base', '<strong>Edit</strong> page'); ?></h1>
                <?php else: ?>
                    <h1><?php echo Yii::t('WikiModule.base', '<strong>Create</strong> new page'); ?></h1>
                <?php endif; ?>

                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'pages-edit-form',
                    'enableAjaxValidation' => false,
                ));
                ?>

                <?php // echo $form->errorSummary($page); ?>

                <?php if ($page->canAdminister() || $page->isNewRecord): ?>
                    <div class="form-group">
                        <?php // echo $form->labelEx($page, 'title'); ?>
                        <?php echo $form->textField($page, 'title', array('class' => 'form-control', 'placeholder' => Yii::t('WikiModule.base', 'New page title'))); ?>
                        <?php echo $form->error($page, 'title'); ?>
                    </div>
                <?php else: ?>
                    <?php echo $form->hiddenField($page, 'title'); ?>
                <?php endif; ?>


                <div class="form-group">
                    <?php // echo $form->labelEx($revision, 'content'); ?>
                    <?php echo $form->textArea($revision, 'content', array('id' => 'txtWikiPageContent', 'rows' => '15', 'placeholder' => Yii::t('WikiModule.base', 'Content'))); ?>

                    <?php echo CHtml::hiddenField('fileUploaderHiddenGuidField', "", array('id' => 'fileUploaderHiddenGuidField')); ?>
                    <div class="modal fade" id="addImageModal" tabindex="-1" role="dialog"
                         aria-labelledby="addImageModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="addImageModalLabel">Add image/file</h4>
                                </div>
                                <div class="modal-body">

                                    <div id="addImageModalUploadForm">
                                        <input id="fileUploaderButton" type="file"
                                               name="files[]"
                                               data-url="<?php echo Yii::app()->createUrl('//file/file/upload', array()); ?>"
                                               multiple>
                                    </div>

                                    <div id="addImageModalProgress">
                                        <strong>Please wait while uploading....</strong>
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="addLinkModal" tabindex="-1" role="dialog"
                         aria-labelledby="addLinkModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="addLinkModalLabel">Add link</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="addLinkTitle">Link title</label>
                                        <input type="text" class="form-control" id="addLinkTitle"
                                               placeholder="Title of your link">
                                    </div>
                                    <div class="form-group">
                                        <label for="addLinkTarget">Target</label>
                                        <input type="text" class="form-control" id="addLinkTarget"
                                               placeholder="Enter wiki page title or url (e.g. http://example.com)">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" id="addLinkButton" class="btn btn-primary">Add link</button>
                                </div>
                            </div>
                        </div>
                    </div>

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
                <?php echo CHtml::submitButton(Yii::t('WikiModule.base', 'Save'), array('class' => 'btn btn-primary')); ?>
                <?php $this->endWidget(); ?>
            </div>

            <div class="col-md-2 wiki-menu">
                <?php if (!$page->isNewRecord): ?>

                    <ul class="nav nav-pills nav-stacked" style="max-width: 300px;">
                        <?php if ($page->canAdminister()): ?>
                            <!-- load modal confirm widget -->
                            <li><?php $this->widget('application.widgets.ModalConfirmWidget', array(
                                    'uniqueID' => 'modal_pagedelete_' . $page->id,
                                    'linkOutput' => 'a',
                                    'title' => Yii::t('WikiModule.base', '<strong>Confirm</strong> page deleting'),
                                    'message' => Yii::t('WikiModule.base', 'Do you really want to delete this page?'),
                                    'buttonTrue' => Yii::t('WikiModule.base', 'Delete'),
                                    'buttonFalse' => Yii::t('WikiModule.base', 'Cancel'),
                                    'linkContent' => '<i class="fa fa-trash-o delete"></i> ' . Yii::t('WikiModule.base', 'Delete'),
                                    'linkHref' => $this->createContainerUrl('//wiki/page/delete', array('id' => $page->id)),
                                    'confirmJS' => 'function(jsonResp) { window.location.href = "'. $this->createContainerUrl('index') .'"; }'
                                ));

                                ?></li>





                        <?php endif; ?>

                        <!--<li><?php /*echo CHtml::link('<i class="fa fa-clock-o history"></i> ' . Yii::t('WikiModule.base', 'Page History'), $this->createContainerUrl('//wiki/page/history', array('id' => $page->id))); */ ?></li>-->
                        <li><?php echo CHtml::link('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Cancel'), $this->createContainerUrl('//wiki/page/view', array('title' => $page->title))); ?></li>
                        <li class="nav-divider"></li>
                        <?php if ($homePage !== null) : ?>
                            <li><?php echo CHtml::link('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $this->createContainerUrl('//wiki/page/index', array())); ?></li>
                        <?php endif; ?>
                        <li><?php echo CHtml::link('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $this->createContainerUrl('//wiki/page/list', array())); ?></li>
                    </ul>

                <?php else: ?>
                    <ul class="nav nav-pills nav-stacked" style="max-width: 300px;">
                        <li><?php echo CHtml::link('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Cancel'), $this->createContainerUrl('//wiki/page/list', array('title' => $page->title))); ?></li>
                        <li class="nav-divider"></li>
                        <?php if ($homePage !== null) : ?>
                            <li><?php echo CHtml::link('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $this->createContainerUrl('//wiki/page/index', array())); ?></li>
                        <?php endif; ?>
                        <li><?php echo CHtml::link('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $this->createContainerUrl('//wiki/page/list', array())); ?></li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>


<link rel="stylesheet" type="text/css" media="screen"
      href="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown/css/bootstrap-markdown.min.css">
<script src="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown/js/bootstrap-markdown.js"></script>
<script>
    wikiPreviewUrl = "<?php echo $this->createContainerUrl('preview'); ?>";
</script>

<script src="<?php echo $this->getModule()->getAssetsUrl(); ?>/edit.js"></script>
<link rel="stylesheet" type="text/css"
      href="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown-override.css">
