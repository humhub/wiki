<?php

use humhub\modules\wiki\models\WikiPageSearch;
use humhub\modules\wiki\widgets\WikiSearchInput;
use yii\widgets\ActiveForm;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $contentContainer \humhub\modules\content\components\ContentActiveRecord */

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
