<?php

use humhub\modules\wiki\widgets\WikiEditor;
use yii\bootstrap\ActiveForm;
use humhub\modules\wiki\widgets\WikiLinkModal;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\Button;

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
        <div class="row <?= $page->is_category ? 'wiki-category-page-edit' : '' ?>">

            <?php WikiContent::begin(['title' => $title, 'id' => 'wiki-page-edit', 'cols' => 10]) ?>

                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                    <?php if ($canAdminister || $page->isNewRecord): ?>
                        <?= $form->field($page, 'title')->textInput(['placeholder' => Yii::t('WikiModule.views_page_edit', 'New page title')])->label(false); ?>
                    <?php else: ?>
                        <?= $form->field($page, 'title')->hiddenInput()->label(false); ?>
                    <?php endif; ?>

                    <?= $form->field($revision, 'content')->widget(WikiEditor::class)->label(false) ?>

                    <?php if ($canAdminister): ?>
                        <?= $form->field($page, 'is_home')->checkbox(); ?>
                        <?= $form->field($page, 'admin_only')->checkbox(); ?>
                        <?= $form->field($page, 'is_category')->checkbox(); ?>
                        <?php if ($hasCategories): ?>
                            <?= $form->field($page, 'parent_page_id')->dropDownList($page->getCategoryList()); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <hr>

                    <?= Button::save()->submit() ?>

                <?php ActiveForm::end(); ?>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $page, 'edit' => true, 'cols' => 2]) ?>

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