<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */
/* @var $this yii\web\View */
/* @var $model \humhub\modules\wiki\models\DefaultSettings */

\humhub\modules\wiki\assets\Assets::register($this);

use humhub\widgets\ActiveForm;
use humhub\widgets\Button;
use \yii\helpers\Html;
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('WikiModule.base', 'Default wiki settings'); ?></div>

    <div class="panel-body" data-ui-widget="calendar.Form">
        <?php $form = ActiveForm::begin(['action' => $model->getSubmitUrl()]); ?>
            <div class="help-block">
                <?= Yii::t('WikiModule.base', 'Here you can configure default settings the wiki module.') ?>
            </div>

            <hr>

            <?= $form->field($model, 'module_label')->textInput(['maxlength' => 50]) ?>

            <?= Button::primary(Yii::t('base', 'Save'))->submit() ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
