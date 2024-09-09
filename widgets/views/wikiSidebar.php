<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\modules\wiki\widgets\CategoryListView;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiSearchForm;
use humhub\widgets\Button;
use humhub\widgets\Link;

/* @var $options array */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */

$settings = new DefaultSettings(['contentContainer' => $contentContainer]);
?>

<?= Html::beginTag('div', $options) ?>
<div class="panel panel-default">
    <div class="panel-body">
        <?php WikiContent::begin(['cssClass' => 'wiki-page-content']) ?>
        <div class="wiki-page-content-header">
            <h3><?= Link::to(Html::encode($settings->module_label), Url::toLastEdited($contentContainer)) ?></h3>
            <?= Button::info()->icon('fa-plus')->link(Url::toWikiCreate($contentContainer))->cssClass('btn-add-page')->sm() ?>
            <?= WikiSearchForm::widget(['contentContainer' => $contentContainer]) ?>
            <div class="wiki-page-content-header-actions">
                <?= Button::info(Yii::t('WikiModule.base', 'Last edited'))->sm()->link(Url::toLastEdited($contentContainer))->cssClass('hidden-lg') ?>
                <?php if ($canCreate) : ?>
                    <?= Button::info(Yii::t('WikiModule.base', 'Create page'))->icon('fa-plus')->link(Url::toWikiCreate($contentContainer))->cssClass('hidden-lg')->sm() ?>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

        <?php WikiContent::end() ?>
    </div>
</div>
<?= Html::endTag('div') ?>
