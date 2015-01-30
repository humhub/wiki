<div class="col-md-8">

    <div class="panel panel-default">
        <div class="panel-body">

            <?php if (!$page->isNewRecord) : ?>
                <h1><?php echo Yii::t('WikiModule.base', 'Edit page'); ?></h1>
            <?php else: ?>
                <h1><?php echo Yii::t('WikiModule.base', 'Create new page'); ?></h1>
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
                <?php echo $form->textArea($revision, 'content', array('class' => 'form-control', 'id' => 'txtWikiPageContent', 'rows' => '15', 'placeholder' => Yii::t('WikiModule.base', 'Content'))); ?>

                <?php echo CHtml::hiddenField('fileUploaderHiddenGuidField', "", array('id' => 'fileUploaderHiddenGuidField')); ?>
                <div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="addImageModalLabel">Add image/file</h4>
                            </div>
                            <div class="modal-body">

                                <div id="addImageModalUploadForm">
                                    <input id="fileUploaderButton" class="btn btn-primary" type="file" name="files[]"
                                           data-url="<?php echo Yii::app()->createUrl('//file/file/upload', array()); ?>" multiple>
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

                <div class="modal fade" id="addLinkModal" tabindex="-1" role="dialog" aria-labelledby="addLinkModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="addLinkModalLabel">Add link</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="addLinkTitle">Link title</label>
                                    <input type="text" class="form-control" id="addLinkTitle" placeholder="Title of your link">
                                </div>
                                <div class="form-group">
                                    <label for="addLinkTarget">Target</label>
                                    <input type="text" class="form-control" id="addLinkTarget" placeholder="Enter wiki page title or url (e.g. http://example.com)">
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

            <?php echo CHtml::submitButton(Yii::t('WikiModule.base', 'Save'), array('class' => 'btn btn-primary')); ?>
            <?php $this->endWidget(); ?>

        </div>
    </div>   
</div>

<?php if (!$page->isNewRecord): ?>
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php if ($page->canAdminister()): ?>
                    <?php echo HHtml::postLink(Yii::t('WikiModule.base', 'Delete'), $this->createContainerUrl('//wiki/page/delete', array('id' => $page->id)), array('class' => 'btn btn-danger', 'confirm' => Yii::t('WikiModule.base', 'Really sure?'))); ?>
                    <br /><br />
                <?php endif; ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Page History'), $this->createContainerUrl('//wiki/page/history', array('id' => $page->id)), array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Back to page'), $this->createContainerUrl('//wiki/page/view', array('title' => $page->title)), array('class' => 'btn btn-xs btn-primary')); ?>

            </div>
        </div>

    </div>
<?php endif; ?>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown/css/bootstrap-markdown.min.css">
<script src="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown/js/bootstrap-markdown.js"></script>
<script>
    wikiPreviewUrl = "<?php echo $this->createContainerUrl('preview'); ?>";
</script>    
<script src="<?php echo $this->getModule()->getAssetsUrl(); ?>/edit.js"></script>
