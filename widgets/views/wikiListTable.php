<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\GridView;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;
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
                return Html::tag('strong', Link::to($model->title, Url::toWiki($model)), ['class' => 'wiki-page-list-row-title'])
                    . '<div class="wiki-page-list-row-details">'
                    . TimeAgo::widget(['timestamp' => $model->content->updated_at])
                    . ' &middot; ' . Html::encode($model->content->updatedBy->displayName)
                    . ' &middot; ' . Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWikiHistory($model))
                    . '</div>';
            }
        ],
    ],
]); ?>
<?= Html::endTag('div') ?>