<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiEditor;
use humhub\modules\wiki\assets\Assets;
use humhub\widgets\Modal;


/** @var $model \humhub\modules\wiki\models\WikiTemplate */
Assets::register($this);
?>

<div class="panel panel-default">
    <div class="panel-body">
        <h1><?= Html::encode(Yii::t('WikiModule.base', 'Append Content')) ?></h1>
        <?= Modal::widget([
                'id' => 'appendablePlaceholderModal',
                'header' => Yii::t('WikiModule.base', '<strong>Fill in Placeholders</strong>'),
                'body' => '<div id="appendablePlaceholderFormContainer">
                    <form id="appendablePlaceholderForm">
                        <div id="appendablePlaceholderFormFields"></div>
                    <button type="submit" class="btn btn-primary mt-2">'.Yii::t('WikiModule.base', 'Insert') .'</button>
                    </form>
                </div>',
                'footer' => false
            ]); ?>
        <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => [
                    'data-ui-widget' => 'wiki.Form',
                    'data-ui-init' => '1'],
                ]); ?>
        
        <div id="append-editor" data-url-editing-status="<?= Html::encode(Url::toWikiEditingStatus($appendForm->page)) ?>" data-url-append-content = <?=Url::toWikiGetAppendContent($appendForm->page);?> >
            <?= $form->field($appendForm->page, 'title')
                        ->textInput([
                            'disabled' => true,
                        ])->label(false); ?>
            
            <?= $form->field($appendForm, 'content')->widget(WikiEditor::class) ?>
        <div>
        <?= Button::save(Yii::t('WikiModule.base', 'Append'))
        ->id('append-save-button')
        ->submit()
        ->options(['data-original-label' => Yii::t('WikiModule.base', 'Append')]) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
