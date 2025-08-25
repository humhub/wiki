<?php

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\wiki\models\WikiPageSearch;

/* @var $this View */
/* @var $options string*/
/* @var $field string*/
/* @var $items array*/
/* @var $model WikiPageSearch*/
?>

<?= Html::activeDropDownList($model, $field, $items, $options); ?>
