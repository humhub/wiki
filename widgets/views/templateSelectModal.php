<?php

use humhub\widgets\Button;
use humhub\widgets\Modal;
use humhub\widgets\ModalButton;
use yii\helpers\Html;
use humhub\modules\wiki\helpers\Url;
use yii\widgets\ActiveForm;

$templateOptions = [];

foreach ($templates as $template) {
    $templateOptions[$template->id] = Html::encode($template->title);
}

?>

<?= Modal::widget([
    'id' => 'templateSelectModal',
    'header' => Yii::t('WikiModule.base','<strong>New page</strong>'),
    'body' => '<div class="text-center">' .Yii::t('WikiModule.base', '<p>Start with an empty page or template<p>') .
                Button::primary(Yii::t('WikiModule.base', 'Create blank page'))->id('blankPageBtn')->loader(false) .
                '<div class="d-flex align-items-center my-3">
                    <span class="mx-2 text-muted">'.Yii::t('WikiModule.base', 'OR').'</span>
                    <hr class="flex-grow-1">
                </div>' .
            '</div>',
    'footer' => 
        Yii::t('WikiModule.base', '<span>List of templates</span>').
        '<select id="templateSelectDropdown" name="templateSelectDropdown" class="form-control" style="margin-bottom: 20px;">' .
        implode('', array_map(function($template) {
            $url = Url::toWikiGetTemplateContent($template);
            return Html::tag('option', Html::encode($template->title), [
                'value' => $template->id,
                'data-url' => $url,
                'name' => $template->title
            ]);
        }, $templates)) .
        '</select>' .
        Button::primary(Yii::t('WikiModule.base', 'Create from template'))->id('useTemplateBtn')->loader(false)

]); ?>
