
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown/css/bootstrap-markdown.min.css">
<script src="<?php echo $this->getModule()->getAssetsUrl(); ?>/bootstrap-markdown/js/bootstrap-markdown.js"></script>



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

                <!-- Add Image Modal -->

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













<script>

    // Newly uploaded file
    var newFile = "";

    $("#txtWikiPageContent").markdown({
        iconlibrary: 'fa',
        additionalButtons: [
            [{
                    name: "groupCustom",
                    data: [{
                            name: "cmdBeer",
                            toggle: true, // this param only take effect if you load bootstrap.js
                            title: "Beer",
                            icon: "glyphicon glyphicon-glass",
                            callback: function(e) {

                                // Replace selection with some drinks
                                var chunk, cursor,
                                        selected = e.getSelection(), content = e.getContent(),
                                        drinks = ["Heinekken", "Budweiser",
                                            "Iron City", "Amstel Light",
                                            "Red Stripe", "Smithwicks",
                                            "Westvleteren", "Sierra Nevada",
                                            "Guinness", "Corona", "Calsberg"],
                                        index = Math.floor((Math.random() * 10) + 1)


                                // Give random drink
                                chunk = drinks[index]

                                // transform selection and set the cursor into chunked text
                                e.replaceSelection(chunk)
                                cursor = selected.start

                                // Set the cursor
                                e.setSelection(cursor, cursor + chunk.length)
                            }
                        },
                        {
                            name: "cmdImgWiki",
                            title: "Add image",
                            icon: {glyph: 'glyphicon glyphicon-picture', fa: 'fa fa-picture-o', 'fa-3': 'icon-picture'},
                            callback: function(e) {
                                newFile = "";
                                $('#addImageModal').modal('show');
                                $('#addImageModalUploadForm').show();
                                $('#addImageModalProgress').hide();
                                $('#addImageModal').on('hide.bs.modal', function(ee) {
                                    if (newFile != "") {
                                        chunk = "![image](" + newFile.url + ")";
                                        selected = e.getSelection(), content = e.getContent(),
                                                e.replaceSelection(chunk);
                                        cursor = selected.start;
                                        e.setSelection(cursor, cursor + chunk.length);
                                    }
                                })
                            }
                        },
                    ]
                }]
        ],
        reorderButtonGroups: ["groupFont", "groupCustom", "groupMisc"],
        onPreview: function(e) {
            var previewContent = "<h1>Test content parsed by backend</h1>aaaa"

            return previewContent;
        }
    });

    $('#fileUploadProgress').hide();
    $('#fileUploaderButton').fileupload({
        dataType: 'json',
        done: function(e, data) {
            $.each(data.result.files, function(index, file) {
                if (!file.error) {
                    newFile = file;

                    hiddenValueField = $('#fileUploaderHiddenGuidField');
                    hiddenValueField.val(hiddenValueField.val() + "," + file.guid);

                    $('#addImageModal').modal('hide');
                } else {
                    alert("file upload error");
                }
            });
        },
        progressall: function(e, data) {
            newFile = "";
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#addImageModalUploadForm').hide();
            $('#addImageModalProgress').show();
            if (progress == 100) {
                $('#addImageModalProgress').hide();
                $('#addImageModalUploadForm').hide();
            }
        }
    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

</script>