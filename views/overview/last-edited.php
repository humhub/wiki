<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\widgets\Image;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;
use yii\data\ActiveDataProvider;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */
/* @var $dataProvider ActiveDataProvider */
?>
<div class="panel panel-default wiki-page-list-table">
    <div class="panel-body">
        <?= Button::info(Yii::t('WikiModule.base', 'Create page'))->icon('fa-plus')
            ->link(Url::toWikiCreate($contentContainer))->visible($canCreate)
            ->right()->sm()->style('margin:10px 20px') ?>
        <h3><?= Icon::get('list-ol') ?> <?= Yii::t('WikiModule.base', 'Last Edited') ?></h3>

        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'showHeader' => false,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => function (WikiPage $model) {
                            return Html::tag('strong', Link::to($model->title, Url::toWiki($model))) . '<br>'
                                . TimeAgo::widget(['timestamp' => $model->content->created_at])
                                . '<small> - '
                                    . Yii::t('WikiModule.base', 'Created by {author}', ['author' => Html::containerLink($model->content->createdBy)])
                                    . ' - ' . Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWikiHistory($model))
                                . '</small>';
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
    </div>
</div>