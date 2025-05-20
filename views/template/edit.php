<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;


/** @var $model \humhub\modules\wiki\models\WikiTemplate */

$this->title = $model->isNewRecord ? Yii::t('WikiModule.base', 'Create Template') : Yii::t('WikiModule.base', 'Edit Template');
?>

<div class="panel panel-default">
    <div class="panel-body">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'content')->widget(ProsemirrorRichTextEditor::class) ?>

        <div class="form-group">
            <?= Button::save()->submit() ?>
            <?= Button::defaultType('Cancel')->link(Url::toWikiTemplateIndex())?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
