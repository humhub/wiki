<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\wiki\widgets\CategoryListView;
use humhub\widgets\Button;

/* @var $this \humhub\components\View */
/* @var $homePage boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $canCreate bool */

humhub\modules\wiki\assets\Assets::register($this);

$homeUrl = $contentContainer->createUrl('/wiki/page/index');
$createUrl = $contentContainer->createUrl('//wiki/page/edit');
?>
<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">
                <h1><?= Yii::t('WikiModule.base', '<strong>Index</strong>'); ?></h1>
                <hr>

                <?= CategoryListView::widget(['contentContainer' => $contentContainer]) ?>

            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">

                <ul class="nav nav-pills nav-stacked">
                    <?php if ($canCreate) : ?>
                        <li>
                            <?= Button::asLink(Yii::t('WikiModule.base', 'New page'), $createUrl)->icon('fa-file-text-o new')?>
                        </li>
                    <?php endif; ?>

                    <?php if ($homePage) : ?>
                        <li class="nav-divider"></li>
                        <li><?= Button::asLink(Yii::t('WikiModule.base', 'Main page'), $homeUrl)->icon('fa-newspaper-o') ?></li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>
</div>