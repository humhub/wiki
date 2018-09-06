<?php

use humhub\modules\wiki\models\WikiPageSearch;
use humhub\modules\wiki\widgets\WikiSearchDropdown;
use yii\widgets\ActiveForm;

/* @var $this \humhub\components\View */

$model = new WikiPageSearch();
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'title')->widget(WikiSearchDropdown::class, ['placeholder' => Yii::t('WikiModule.base', 'Search for Wiki Title')])?>

<?php ActiveForm::end() ?>
