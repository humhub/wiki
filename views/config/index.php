<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\wiki\models\ConfigForm;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\form\ContentHiddenCheckbox;

/* @var $model ConfigForm */
?>
<div class="card content-card">
    <div class="card-header"><div class="card-title"><?= Yii::t('WikiModule.base', '<strong>Wiki</strong> module configuration') ?></div></div>
    <div class="card-body">
        <?php $form = ActiveForm::begin() ?>

        <?= $form->field($model, 'contentHiddenDefault')->widget(ContentHiddenCheckbox::class, [
            'type' => ContentHiddenCheckbox::TYPE_GLOBAL,
        ]) ?>

        <?= $form->field($model, 'hideNavigationEntryDefault')->checkbox() ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
