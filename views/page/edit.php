<?php

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\form\widgets\ContentVisibilitySelect;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\models\forms\PageEditForm;
use humhub\modules\wiki\widgets\WikiEditor;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\wiki\widgets\WikiLinkModal;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\Button;
use humhub\modules\topic\widgets\TopicPicker;

/* @var $this View */
/* @var $model PageEditForm */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $requireConfirmation bool */
/* @var $diffUrl string */
/* @var $discardChangesUrl string */

humhub\modules\wiki\assets\Assets::register($this);

$canAdminister = $model->canAdminister();

?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row <?= $model->page->is_category ? 'wiki-category-page-edit' : '' ?>">

            <?php WikiContent::begin([
                'title' => $model->getTitle(),
                'id' => 'wiki-page-edit',
                'cols' => $requireConfirmation ? 12 : 9,
            ]) ?>

            <?php $form = ActiveForm::begin(
                ['enableClientValidation' => false, 'options' => [
                    'data-ui-widget' => 'wiki.Form',
                    'data-change-category-confirm' => Yii::t('WikiModule.base', 'Are you really sure? All existing category page assignments will be removed!'),
                    'data-is-category' => $model->page->is_category,
                    'data-ui-init' => '1'],
                    'acknowledge' => true
                ]
            ); ?>

            <?= $form->field($model, 'latestRevisionNumber')->hiddenInput()->label(false); ?>
            <?php if ($requireConfirmation) : ?>
                <div class="alert alert-danger">
                    <?= Yii::t('WikiModule.base',
                        '<strong>Warning!</strong><br><br>Another user has updated this page since you have started editing it. Please confirm that you want to overwrite those changes.<br>:linkToCompare', [
                            ':linkToCompare' => Button::asLink('<i class="fa fa-arrow-right"></i>&nbsp;' . Yii::t('WikiModule.base', 'Compare changes'))->action('compareOverwriting', $diffUrl)->cssClass('colorDanger')
                        ]); ?>
                </div>
                <?= $form->field($model, 'backOverwriting')->hiddenInput()->label(false); ?>
                <?= $form->field($model, 'confirmOverwriting')->checkbox()->label(); ?>

                <?= Button::save(Yii::t('WikiModule.base', 'Overwrite'))->submit() ?>

                <?= Button::defaultType(Yii::t('WikiModule.base', 'Back'))->action('backOverwriting')->icon('back')->loader(false); ?>

                <div class="pull-right">
                    <?= Button::danger(Yii::t('WikiModule.base', 'Discard my changes'))->link($discardChangesUrl)->icon('close')->loader(true); ?>
                </div>
            <?php else : ?>
                <?= $form->field($model, 'confirmOverwriting')->hiddenInput()->label(false); ?>
            <?php endif; ?>

            <div<?php if ($requireConfirmation) : ?> style="display:none"<?php endif; ?>>

                <?= $form->field($model->page, 'title')
                    ->textInput([
                        'placeholder' => Yii::t('WikiModule.base', 'New page title'),
                        'disabled' => $model->isDisabledField('title')
                    ])->label(false); ?>

                <?= $form->field($model->revision, 'content')->widget(WikiEditor::class)->label(false) ?>


                <?= $form->beginCollapsibleFields('Advanced settings'); ?>

                <?php if (!$canAdminister) : ?>
                    <div class="alert alert-info">
                        <?= Yii::t('WikiModule.base',
                            'In order to edit all fields, you need the permission to administer wiki pages.'); ?>
                    </div>
                <?php endif; ?>

                <?= $form->field($model->page, 'is_home')->checkbox([
                    'title' => Yii::t('WikiModule.base', 'Overwrite the wiki index start page?'),
                    'disabled' => $model->isDisabledField('is_home')]); ?>

                <?= $form->field($model->page, 'is_category')->checkbox(['disabled' => $model->isDisabledField('is_category')]); ?>

                <?= $form->field($model->page, 'parent_page_id')
                    ->dropDownList($model->getCategoryList())
                    ->label($model->page->is_category ? Yii::t('WikiModule.base', 'Parent category') : null); ?>

                <?= $form->field($model, 'isPublic')->widget(ContentVisibilitySelect::class, [
                    'readonly' => $model->isDisabledField('isPublic')]); ?>

                <?= $form->field($model->page, 'admin_only')->checkbox([
                    'title' => Yii::t('WikiModule.base', 'Disable edit access for non wiki administrators?'),
                    'disabled' => $model->isDisabledField('admin_only')]); ?>

                <?= $form->field($model->page, 'is_container_menu')->checkbox([
                    'disabled' => $model->isDisabledField('is_container_menu')]); ?>
                <div id="container_menu_order_field"<?php if (!$model->page->is_container_menu) : ?> style="display: none"<?php endif; ?>>
                    <?= $form->field($model->page, 'container_menu_order')->textInput([
                        'disabled' => $model->isDisabledField('container_menu_order')]); ?>
                </div>

                <?= $form->endCollapsibleFields(); ?>

                <?= $form->field($model, 'topics')->widget(TopicPicker::class, ['options' => ['disabled' => $model->isDisabledField('topics')]])->label(false) ?>

                <hr>

                <?= Button::save()->submit() ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php WikiContent::end() ?>

            <?php if (!$requireConfirmation) : ?>
                <?= WikiMenu::widget(['page' => $model->page, 'edit' => true]) ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?= WikiLinkModal::widget(['contentContainer' => $contentContainer]) ?>

<script <?= Html::nonce() ?>>
    $('input[name="WikiPage[is_container_menu]"]').click(function () {
        $('#container_menu_order_field').toggle($(this).prop('checked'));
    })
</script>
