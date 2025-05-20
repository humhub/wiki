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
    'header' => '<strong>New page</strong>',
    'body' => '<div class="text-center">' .Yii::t('WikiModule.base', '<p>Start with an empty page or template<p>') .
                Button::primary(Yii::t('WikiModule.base', 'Create blank page'))->id('blankPageBtn')->loader(false) .
                '<div class="d-flex align-items-center my-3">
                    <span class="mx-2 text-muted">OR</span>
                    <hr class="flex-grow-1">
                </div>' .
            '</div>',
    'footer' => 
        '<select id="templateSelectDropdown" class="form-control" style="margin-bottom: 20px;">' .
        '<option value="">' . Yii::t('WikiModule.base', 'List of templates') . '</option>' .
        implode('', array_map(function($template) {
            $url = Url::toWikiGetTemplateContent($template);
            return Html::tag('option', Html::encode($template->title), [
                'value' => $template->id,
                'data-url' => $url
            ]);
        }, $templates)) .
        '</select>' .
        Button::primary(Yii::t('WikiModule.base', 'Create from template'))->id('useTemplateBtn')->loader(false)

]); ?>
