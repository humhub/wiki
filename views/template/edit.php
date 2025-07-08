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

$this->registerJSConfig([
    'wiki' => [
        'text' => [
            'name' => Yii::t('WikiModule.base', 'Name'),
            'description' => Yii::t('WikiModule.base', 'Description'),
            'default' => Yii::t('WikiModule.base', 'Default'),
            'type' => Yii::t('WikiModule.base', 'Type'),
            'add' => Yii::t('WikiModule.base', 'Add'),
            'appendableContent' => Yii::t('WikiModule.base', 'For appendable content'),
        ]
    ]
]);

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

        <?= $form->field($model, 'appendable_content_placeholder')->textInput()->hiddenInput()->label(false) ?>
        
        <?= Button::primary(Yii::t('WikiModule.base', 'Create Placeholder'))->action('addPlaceholder')->loader(false)->xs()->right() ?>
        <?= $form->field($model, 'placeholders')->textInput()->hiddenInput() ?>

        <div id="placeholder-table-container">
            <div class="alert alert-info mb-3">
                <strong><?= Yii::t('WikiModule.base', 'Tip:') ?></strong>
                <?= Yii::t('WikiModule.base', 'You can also use special placeholders like {today1}, {today2}, or {author}.', [
                    'today1' => '<code>{{today YYYY-DD-MM}}</code>',
                    'today2' => '<code>{{today DD.MM.YYYY}}</code>',
                    'author' => '<code>{{author}}</code>',
                ]) ?>
            </div>
            <table class="table table-bordered table-sm" id="placeholder-table">
                <colgroup>
                    <col style="width: 15%;">
                    <col style="width: 45%;">
                    <col style="width: 25%;">
                    <col style="width: 10%;">
                    <col style="width: 5%;">
                </colgroup>
                <thead >
                    <tr>
                        <th class="text-center"><?= Yii::t('WikiModule.base', 'Name')?></th>
                        <th class="text-center"><?= Yii::t('WikiModule.base', 'Description')?></th>
                        <th class="text-center"><?= Yii::t('WikiModule.base', 'Default')?></th>
                        <th class="text-center"><?= Yii::t('WikiModule.base', 'Type')?></th>
                        <th class="text-center"><?= Yii::t('WikiModule.base', 'Actions')?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be injected by JS -->
                </tbody>
            </table>
        </div>

        <?= Modal::widget([
                'id' => 'addPlaceholderModal',
                'header' => Yii::t('WikiModule.base', '<strong>Add Placeholder</strong>'),
                'body' => '<div id="newPlaceholderFormContainer"></div>',
                'footer' =>  false
        ]); ?>
        <hr>
        <div class="form-group">
            <?= Button::save()->submit() ?>
            <?= Button::defaultType(Yii::t('WikiModule.base','Cancel'))->link(Url::toWikiTemplateIndex())?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
