<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\wiki\assets\Assets;
use humhub\widgets\Modal;


/** @var $model \humhub\modules\wiki\models\WikiTemplate */
Assets::register($this);

$this->title = $model->isNewRecord ? Yii::t('WikiModule.base', 'Create Template') : Yii::t('WikiModule.base', 'Edit Template');
?>

<div class="panel panel-default">
    <div class="panel-body">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => [
                    'data-ui-widget' => 'wiki.Form',
                    'data-ui-init' => '1'],
                ]); ?>
        
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'title_template')->textInput(['placeholder' => 'e.g., Report for {{project}}']) ?>
        <?= $form->field($model, 'content')->widget(ProsemirrorRichTextEditor::class) ?>
        <?= $form->field($model, 'is_appendable')->checkbox([]) ?>
        <div id="appendable-content-wrapper" style="<?= $model->is_appendable ? '' : 'display: none;' ?>">
            <?= $form->field($model, 'appendable_content')->widget(ProsemirrorRichTextEditor::class) ?>
        </div>
        
        <?= Button::primary(Yii::t('WikiModule.base', 'Create Placeholder'))->action('addPlaceholder')->loader(false)->xs()->right() ?>
        <?= $form->field($model, 'placeholders')->textInput()->hiddenInput() ?>

        <div id="placeholder-table-container">
            <table class="table table-bordered table-sm" id="placeholder-table">
                <colgroup>
                    <col style="width: 15%;">
                    <col style="width: 55%;">
                    <col style="width: 25%;">
                    <col style="width: 5%;">
                </colgroup>
                <thead >
                    <tr>
                        <th class="text-center">Name</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Default</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be injected by JS -->
                </tbody>
            </table>
        </div>

        <?= Modal::widget([
                'id' => 'addPlaceholderModal',
                'header' => '<strong>Add Placeholder</strong>',
                'body' => '<div id="newPlaceholderFormContainer"></div>',
                'footer' =>  false
        ]); ?>

        <div class="form-group">
            <?= Button::save()->submit() ?>
            <?= Button::defaultType('Cancel')->link(Url::toWikiTemplateIndex())?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
