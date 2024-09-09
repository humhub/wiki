<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiSearchForm;
use humhub\widgets\Button;

/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */

Assets::register($this);

$createPageTitle = Yii::t('WikiModule.base', 'Create page');
if (Helper::isEnterpriseTheme()) {
    $createPageTitle = Html::tag('span', $createPageTitle, ['class' => 'hidden-lg']);
}

$settings = new DefaultSettings(['contentContainer' => $contentContainer]);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <?php WikiContent::begin(['cssClass' => 'wiki-page-content']) ?>
            <div class="wiki-page-content-header">
                <h3><?= Html::encode($settings->module_label) ?></h3>
                <?= WikiSearchForm::widget(['contentContainer' => $contentContainer, 'cssClass' => 'pull-left']) ?>
                <div class="wiki-page-content-header-actions">
                    <?= Button::info(Yii::t('WikiModule.base', 'Last edited'))->sm()->link(Url::toLastEdited($contentContainer))->cssClass(Helper::isEnterpriseTheme() ? 'hidden-lg' : '') ?>
                    <?= Button::info($createPageTitle)->icon('fa-plus')->sm()->link(Url::toWikiCreate($contentContainer))->visible($canCreate) ?>
                </div>
                <div class="clearfix"></div>
            </div>

            <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

            <?php WikiContent::end() ?>
        </div>
    </div>
</div>
