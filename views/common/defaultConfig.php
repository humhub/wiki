<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\form\ContentHiddenCheckbox;

/* @var $this View */
/* @var $model DefaultSettings */

Assets::register($this);
?>
<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('WikiModule.base', 'Default wiki settings'); ?></div>

    <div class="panel-body" data-ui-widget="calendar.Form">
        <?php $form = ActiveForm::begin(['action' => $model->getSubmitUrl()]); ?>
            <div class="form-text">
                <?= Yii::t('WikiModule.base', 'Here you can configure default settings the wiki module.') ?>
            </div>

            <hr>

            <?= $form->field($model, 'module_label')->textInput(['maxlength' => 50]) ?>

            <?= $form->field($model, 'contentHiddenDefault')->widget(ContentHiddenCheckbox::class, [
                'type' => ContentHiddenCheckbox::TYPE_CONTENTCONTAINER,
            ]) ?>

            <?= $form->field($model, 'hideNavigationEntry')->checkbox() ?>

            <?= Button::primary(Yii::t('base', 'Save'))->submit() ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
