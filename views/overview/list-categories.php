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
use humhub\modules\wiki\permissions\AdministerPages;

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
                    <?php
                        $module = Yii::$app->getModule('wiki');    
                        $user = Yii::$app->user->identity;
                        $numberingEnabled = $module->settings->contentContainer($user)->get('overviewNumberingEnabled');
                        $editingEnabled = $module->settings->contentContainer($user)->get('wikiTreeEditingEnabled', FALSE);
                    ?>
                    <span class="dropdown">
                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true">
                        <i class="fa fa-cog"></i>    
                        <?= Yii::t('WikiModule.base', 'Options') ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a href="<?= Url::current(['toggle-numbering']) ?>" class="toggle-numbering">
                                    <?= $numberingEnabled ? Yii::t('WikiModule.base', 'Disable Numbering') : Yii::t('WikiModule.base', 'Enable Numbering') ?>
                                </a>
                            </li>
                            <?php if ($contentContainer->can(AdministerPages::class)): ?>
                                <li>
                                <a href="<?= Url::current(['toggle-wiki-tree-editing']) ?>" class="toggle-editing">
                                    <?= $editingEnabled ? Yii::t('WikiModule.base', 'Disable wiki tree editing') : Yii::t('WikiModule.base', 'Enable wiki tree editing') ?>
                                </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="<?= Url::toWikiTemplateIndex() ?>" class="manage-template">
                                    <?= Yii::t('WikiModule.base', 'Manage Template') ?>
                                </a>
                            </li>
                        </ul>
                    </span>
                </div>
                <div class="clearfix"></div>
            </div>

            <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

            <?php WikiContent::end() ?>
        </div>
    </div>
</div>
