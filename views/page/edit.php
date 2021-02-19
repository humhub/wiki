<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
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

humhub\modules\wiki\assets\Assets::register($this);

$canAdminister = $model->canAdminister();

?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row <?= $model->page->is_category ? 'wiki-category-page-edit' : '' ?>">

            <?php WikiContent::begin(['title' => $model->getTitle(), 'id' => 'wiki-page-edit']) ?>

                <?php $form = ActiveForm::begin(
                    ['enableClientValidation' => false, 'options' => [
                            'id' => 'wiki-edit-form',
                        'data-ui-widget' => 'wiki.Form',
                        'data-change-category-confirm' => Yii::t('WikiModule.base', 'Are you really sure? All existing category page assignments will be removed!'),
                        'data-is-category' => $model->page->is_category,
                        'data-ui-init' => '1'],
                        'acknowledge' => true
                    ]
                ); ?>

                    <?= $form->field($model->page, 'title')
                        ->textInput([
                            'placeholder' => Yii::t('WikiModule.views_page_edit', 'New page title'),
                            'disabled' => $model->isDisabledField('title')
                        ])->label(false); ?>

                    <?= $form->field($model->revision, 'content')->widget(WikiEditor::class)->label(false) ?>

                    <?= $form->field($model->page, 'is_home')->checkbox([
                             'title' => Yii::t('WikiModule.base', 'Overwrite the wiki index start page?'),
                            'disabled' => $model->isDisabledField('is_home')]); ?>
                    <?= $form->field($model->page, 'admin_only')->checkbox([
                            'title' => Yii::t('WikiModule.base', 'Disable edit access for non wiki administrators?'),
                            'disabled' => $model->isDisabledField('admin_only')]); ?>
                    <?= $form->field($model->page, 'is_category')->checkbox(['disabled' => $model->isDisabledField('is_category')]); ?>
                    <?= $form->field($model, 'isPublic')->checkbox([
                            'title' => Yii::t('WikiModule.base', 'Enable read access for non space members?'),
                            'disabled' => $model->isDisabledField('isPublic')]); ?>

                    <?php if(!$model->isDisabledField('is_category') || $model->page->parent_page_id) :?>
                        <?= $form->field($model->page, 'parent_page_id')
                            ->dropDownList($model->getCategoryList(), ['disabled' => $model->isDisabledField('parent_page_id')]); ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'topics')->widget(TopicPicker::class, ['options' => ['disabled' =>  $model->isDisabledField('topics')]])->label(false) ?>

                    <?php if(!$canAdminister) : ?>
                        <div class="alert alert-warning">
                            <?= Yii::t('WikiModule.base',
                                'In order to edit all fields, you need the permission to administer wiki pages.'); ?>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <?= Button::save()->submit() ?>

                <?php ActiveForm::end(); ?>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $model->page, 'edit' => true]) ?>

        </div>
    </div>
</div>

<?= WikiLinkModal::widget(['contentContainer' => $contentContainer]) ?>
