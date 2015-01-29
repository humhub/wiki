<div class="col-md-8">

    <div class="panel panel-default">
        <div class="panel-body">

            <?php if (!$page->isNewRecord) : ?>
                <h1><?php echo Yii::t('PageModule.base', 'Edit page'); ?></h1>
            <?php else: ?>
                <h1><?php echo Yii::t('PageModule.base', 'Create new page'); ?></h1>
            <?php endif; ?>


            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'pages-edit-form',
                'enableAjaxValidation' => false,
            ));
            ?>

            <?php //echo $form->errorSummary($page); ?>

            <div class="form-group">
                <?php echo $form->labelEx($page, 'title'); ?>
                <?php echo $form->textField($page, 'title', array('class' => 'form-control', 'placeholder' => Yii::t('WikiModule.base', 'New page title'))); ?>
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <?php echo $form->checkBox($page, 'is_home', array()); ?> <?php echo $page->getAttributeLabel('is_home'); ?>
                    </label>
                </div>
            </div>            

            <div class="form-group">
                <?php echo $form->labelEx($revision, 'content'); ?>
                <?php echo $form->textArea($revision, 'content', array('class' => 'form-control', 'rows' => '15', 'placeholder' => Yii::t('WikiModule.base', 'Content'))); ?>
            </div>

            <?php echo CHtml::submitButton(Yii::t('PageModule.base', 'Save'), array('class' => 'btn btn-primary')); ?>

            <?php $this->endWidget(); ?>


        </div>
    </div>   
</div>

<?php if (!$page->isNewRecord): ?>
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-body">


                <?php echo HHtml::postLink(Yii::t('WikiModule.base', 'Delete'), $this->createContainerUrl('//wiki/page/delete', array('id' => $page->id)), array('class' => 'btn btn-danger', 'confirm' => Yii::t('WikiModule.base', 'Really sure?'))); ?>
                <br /><br />
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Page History'), $this->createContainerUrl('//wiki/page/history', array('id' => $page->id)), array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Back to page'), $this->createContainerUrl('//wiki/page/view', array('title' => $page->title)), array('class' => 'btn btn-xs btn-primary')); ?>

            </div>
        </div>

    </div>
<?php endif; ?>
