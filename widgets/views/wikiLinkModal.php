<?php

use humhub\components\View;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\wiki\models\WikiPageSearch;
use humhub\modules\wiki\widgets\WikiSearchInput;
use humhub\widgets\form\ActiveForm;

/* @var $this View */
/* @var $contentContainer ContentActiveRecord */

$model = new WikiPageSearch();
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'title')->widget(WikiSearchInput::class, [
    'contentContainer' => $contentContainer,
    'placeholder' => Yii::t('WikiModule.base', 'Search for Wiki Title')
])?>

<?= $form->field($model, 'anchor')->dropDownList([])?>

<?= $form->field($model, 'label')->textInput() ?>

<?php ActiveForm::end() ?>
