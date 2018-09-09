<?php
use humhub\libs\Html;
use yii\helpers\Url;

/* @var $this \humhub\components\View */
/* @var $options string*/
/* @var $field string*/
/* @var $items array*/
/* @var $model \humhub\modules\wiki\models\WikiPageSearch*/
?>

<?= Html::activeDropDownList($model, $field, $items, $options); ?>


