<?php

use humhub\modules\wiki\widgets\WikiEditor;
use yii\bootstrap\ActiveForm;
use humhub\modules\wiki\widgets\WikiLinkModal;
use yii\helpers\Html;
use humhub\modules\wiki\helpers\Url;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $revision \humhub\modules\wiki\models\WikiPageRevision */
/* @var $homePage boolean */
/* @var $canAdminister boolean */
/* @var $hasCategories boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

humhub\modules\wiki\assets\Assets::register($this);

$title = ($page->isNewRecord)
    ? Yii::t('WikiModule.views_page_edit', '<strong>Create</strong> new page')
    : Yii::t('WikiModule.views_page_edit', '<strong>Edit</strong> page');

?>
<div class="panel panel-default">

    <div class="panel-body">

        <div class="row">
            <div id="wiki-page-edit" class="col-lg-10 col-md-9 col-sm-9 wiki-content">

                <h1><?= $title ?></h1>

                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                <?php if ($canAdminister || $page->isNewRecord): ?>
                    <?= $form->field($page, 'title')->textInput(['placeholder' => Yii::t('WikiModule.views_page_edit', 'New page title')])->label(false); ?>
                <?php else: ?>
                    <?= $form->field($page, 'title')->hiddenInput()->label(false); ?>
                <?php endif; ?>

                <?= $form->field($revision, 'content')->widget(WikiEditor::class)->label(false) ?>

                <script>
                    $(document).ready(function () {
                        // Fix MarkdownEditor Url Placeholder, user can also insert wiki page title
                        $('.linkTarget').attr("placeholder", "<?= Yii::t('WikiModule.views_page_edit', 'Enter a wiki page name or url (e.g. http://example.com)'); ?>");
                    });
                </script>

                <?php if ($canAdminister): ?>
                    <?= $form->field($page, 'is_home')->checkbox(); ?>
                    <?= $form->field($page, 'admin_only')->checkbox(); ?>
                    <?= $form->field($page, 'is_category')->checkbox(); ?>
                    <?php if ($hasCategories): ?>
                        <?= $form->field($page, 'parent_page_id')->dropDownList($page->getCategoryList()); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <hr>
                <?= Html::submitButton(Yii::t('WikiModule.views_page_edit', 'Save'), array('class' => 'btn btn-primary', 'data-ui-loader' => true)); ?>
                <?php ActiveForm::end(); ?>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">
                <div class="wiki-menu-fixed">
                    <?php if (!$page->isNewRecord): ?>

                        <ul class="nav nav-pills nav-stacked">
                            <?php if ($canAdminister): ?>
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

                            <li><?= Html::a('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Cancel'), Url::toWiki($page)); ?></li>
                            <li class="nav-divider"></li>
                            <?php if ($homePage !== null) : ?>
                                <li><?= Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('//wiki/page/index', array())); ?></li>
                            <?php endif; ?>
                            <li><?= Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $contentContainer->createUrl('//wiki/page/list', array())); ?></li>
                        </ul>

                    <?php else: ?>
                        <ul class="nav nav-pills nav-stacked">
                            <li><?= Html::a('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Cancel'), $contentContainer->createUrl('//wiki/page/list', array('title' => $page->title))); ?></li>
                            <li class="nav-divider"></li>
                            <?php if ($homePage !== null) : ?>
                                <li><?= Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('//wiki/page/index', array())); ?></li>
                            <?php endif; ?>
                            <li><?= Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $contentContainer->createUrl('//wiki/page/list', array())); ?></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?= WikiLinkModal::widget(['contentContainer' => $contentContainer]) ?>

<script>
    $('#wikipage-is_category').click(function () {
        <?php if ($page->is_category): ?>
        if ($(this).is(":not(:checked)")) {
            if (!confirm('<?= Yii::t('WikiModule.base', 'Are you really sure? All existing category page assignments will be removed!'); ?>')) {
                $(this).prop('checked', true);
            }
        }
        <?php endif; ?>
        hideCategorySelect();
    });

    hideCategorySelect();

    function hideCategorySelect() {
        if ($('#wikipage-is_category').is(":not(:checked)")) {
            $('.field-wikipage-parent_page_id').show();
        } else {
            $('.field-wikipage-parent_page_id').hide();
        }
    }
</script>