<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;

/* @var $placeholder string */
/* @var $keyword string */
/* @var $submitUrl string */
/* @var $cssClass string */
?>
<?php ActiveForm::begin(['action' => $submitUrl, 'method' => 'get', 'options' => ['class' => 'wiki-search-form' . ($cssClass ? ' ' . $cssClass : '')]]) ?>

<?= Html::textInput('keyword', $keyword, ['placeholder' => $placeholder, 'class' => 'form-control']) ?>

<?= Button::light(Icon::get('search'))->submit() ?>

<?php ActiveForm::end() ?>
