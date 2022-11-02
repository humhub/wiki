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
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\widgets\WikiSearchForm;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;
use yii\data\ActiveDataProvider;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */
/* @var $dataProvider ActiveDataProvider */

Assets::register($this);
?>
<div class="panel panel-default wiki-page-list-table">
    <div class="panel-body">
        <div class="wiki-page-content-header">
            <h3><?= Icon::get('list-ol') ?> <?= Yii::t('WikiModule.base', 'Last Edited') ?></h3>
            <?= WikiSearchForm::widget(['contentContainer' => $contentContainer, 'cssClass' => Helper::isEnterpriseTheme() ? 'hidden-lg' : '']) ?>
            <div class="wiki-page-content-header-actions">
                <?= Button::info(Yii::t('WikiModule.base', 'Index'))->icon('fa-home')
                    ->link(Url::toHome($contentContainer))->sm()->cssClass(Helper::isEnterpriseTheme() ? 'hidden-lg' : '') ?>
                <?= Button::info(Yii::t('WikiModule.base', 'Create page'))->icon('fa-plus')
                    ->link(Url::toWikiCreate($contentContainer))->visible($canCreate)->sm() ?>
            </div>
            <div class="clearfix"></div>
        </div>

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
                                    . ' - ' . Yii::t('WikiModule.base', 'Created by {author}', ['author' => Html::containerLink($model->content->createdBy)])
                                    . ' - ' . Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWikiHistory($model))
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
    </div>
</div>