<?php
use humhub\libs\Html;
use yii\helpers\Url;

/* @var $this \humhub\components\View */
/* @var $inputId string*/
/* @var $field string*/
/* @var $model \humhub\modules\wiki\models\WikiPageSearch*/
?>

<?= Html::activeTextInput($model, $field, ['class' => 'form-control', 'id' => $inputId, 'value' => $model->$field]); ?>

<script type="text/javascript">
    $('#<?= $inputId ?>').autocomplete({
        source: '<?= Url::to(['/wiki/search/search'])?>',
        minLength: 2,
        select: function(evt, ui) {
            console.log( "Selected: " + ui.item.value + " aka " + ui.item.id );
        }
    })
</script>


