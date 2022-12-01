<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\user\widgets\Image;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\GridView;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;
use yii\data\ActiveDataProvider;

/* @var $dataProvider ActiveDataProvider */
?>

<div class="table-responsive">
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
                    . TimeAgo::widget(['timestamp' => $model->content->created_at])
                    . ' &middot; ' . Yii::t('WikiModule.base', 'Created by {author}', ['author' => Html::containerLink($model->content->createdBy)])
                    . ' &middot; ' . Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWikiHistory($model))
                    . '</div>';
            }
        ],
        [
            'attribute' => 'updated_by',
            'options' => ['width' => '50px'],
            'format' => 'raw',
            'value' => function (WikiPage $model) {
                return Image::widget(['user' => $model->content->updatedBy, 'width' => 24, 'showTooltip' => true]);
            }
        ],
    ],
]); ?>
</div>