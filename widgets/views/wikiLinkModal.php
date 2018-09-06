<?php

use humhub\modules\wiki\models\WikiPageSearch;
use humhub\modules\wiki\widgets\WikiSearchDropdown;
use yii\widgets\ActiveForm;

/* @var $this \humhub\components\View */
$model = new WikiPageSearch();
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'id')->widget(WikiSearchDropdown::class)?>

<?php ActiveForm::end() ?>
