<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\ContentHiddenCheckbox;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\widgets\Button;

/* @var $this View */
/* @var $model DefaultSettings */

Assets::register($this);
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

            <?= $form->field($model, 'contentHiddenDefault')->widget(ContentHiddenCheckbox::class, [
                'type' => ContentHiddenCheckbox::TYPE_CONTENTCONTAINER,
            ]) ?>

            <?= $form->field($model, 'hideNavigationEntry')->checkbox() ?>

            <?= Button::primary(Yii::t('base', 'Save'))->submit() ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
