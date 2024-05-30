<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;

/* @var $dataProvider ActiveDataProvider */
/* @var $options array */
?>
<?= Html::beginTag('div', $options) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => '',
    'showHeader' => false,
    'layout' => "{items}\n<div class='pagination-container'>{pager}</div>",
    'columns' => [
        [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function (WikiPage $model) {
                return $this->render('wikiListTableRow', ['wikiPage' => $model]);
            },
        ],
    ],
]) ?>
<?= Html::endTag('div') ?>
