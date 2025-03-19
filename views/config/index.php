<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\ui\form\widgets\ContentHiddenCheckbox;
use humhub\modules\wiki\models\ConfigForm;
use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var $model ConfigForm */
?>
<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('WikiModule.base', '<strong>Wiki</strong> module configuration') ?></div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'contentHiddenDefault')->widget(ContentHiddenCheckbox::class, [
            'type' => ContentHiddenCheckbox::TYPE_GLOBAL,
        ]) ?>

        <?= $form->field($model, 'hideNavigationEntryDefault')->checkbox() ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
