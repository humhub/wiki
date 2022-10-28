<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Button;
use yii\widgets\ActiveForm;

/* @var $placeholder string */
/* @var $keyword string */
/* @var $submitUrl string */
?>
<?php ActiveForm::begin(['action' => $submitUrl, 'method' => 'get', 'options' => ['class' => 'wiki-search-form']]) ?>

<?= Html::textInput('keyword', $keyword, ['placeholder' => $placeholder, 'class' => 'form-control']) ?>

<?= Button::defaultType(Icon::get('search'))->submit() ?>

<?php ActiveForm::end() ?>