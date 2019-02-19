<?php

use humhub\modules\wiki\widgets\WikiEditor;
use yii\bootstrap\ActiveForm;
use humhub\modules\wiki\widgets\WikiLinkModal;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\Button;
use humhub\modules\topic\widgets\TopicPicker;

/* @var $this \humhub\components\View */
/* @var $model \humhub\modules\wiki\models\forms\PageEditForm */
/* @var $hasCategories boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

humhub\modules\wiki\assets\Assets::register($this);

?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row <?= $model->page->is_category ? 'wiki-category-page-edit' : '' ?>">

            <?php WikiContent::begin(['title' => $model->getTitle(), 'id' => 'wiki-page-edit']) ?>

                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                    <?php if ($model->canAdminister() || $model->isNewPage()): ?>
                        <?= $form->field($model->page, 'title')->textInput(['placeholder' => Yii::t('WikiModule.views_page_edit', 'New page title')])->label(false); ?>
                    <?php else: ?>
                        <?= $form->field($model->page, 'title')->hiddenInput()->label(false); ?>
                    <?php endif; ?>

                    <?= $form->field($model->revision, 'content')->widget(WikiEditor::class)->label(false) ?>

                    <?php if ($model->canAdminister()): ?>
                        <?= $form->field($model->page, 'is_home')->checkbox(); ?>
                        <?= $form->field($model->page, 'admin_only')->checkbox(); ?>
                        <?= $form->field($model->page, 'is_category')->checkbox(); ?>
                        <?= $form->field($model->page, 'is_public')->checkbox(); ?>
                        <?php if ($hasCategories): ?>
                            <?= $form->field($model->page, 'parent_page_id')->dropDownList($model->getCategoryList()); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'topics')->widget(TopicPicker::class)->label(false) ?>

                    <hr>

                    <?= Button::save()->submit() ?>

                <?php ActiveForm::end(); ?>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $model->page, 'edit' => true]) ?>

        </div>
    </div>
</div>

<?= WikiLinkModal::widget(['contentContainer' => $contentContainer]) ?>

<script>
    $('#wikipage-is_category').click(function () {
        <?php if ($model->page->is_category): ?>
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