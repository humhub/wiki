<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\widgets\LinkPager;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $pages \humhub\modules\wiki\models\WikiPage[] a list of pages without category */
/* @var $pagination \humhub\modules\wiki\models\WikiPageRevision */
/* @var $homePage boolean */
/* @var $canAdminister boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

humhub\modules\wiki\Assets::register($this);
?>
<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">
                <h1><?= Yii::t('WikiModule.base', '<strong>Overview</strong> of all pages'); ?></h1>

                <br>
                <ul class="wiki-page-list">

                    <?php foreach ($pages as $page): ?>
                        <li>
                            <?php if ($page->is_category) : ?>
                                <span class="page-category-title">
                                <?= Html::a('<i class="fa fa-list-ol"></i> ' . Html::encode($page->title), $page->getUrl()); ?>
                                    (<?= $page->findChildren()->count(); ?>)
                            </span>
                            <?php else: ?>
                                <span class="page-title">
                                <?= Html::a('<i class="fa fa-file-text-o"></i> ' . Html::encode($page->title), $page->getUrl()); ?>
                            </span>
                            <?php endif; ?>

                        </li>
                    <?php endforeach; ?>

                </ul>

                <div class="text-center">
                    <?= LinkPager::widget(['pagination' => $pagination]); ?>
                </div>

            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">

                <ul class="nav nav-pills nav-stacked">
                    <?php if ($this->context->canCreatePage()): ?>
                        <li>
                            <a href="<?= $contentContainer->createUrl('//wiki/page/edit'); ?>">
                                <i class="fa fa-file-text-o new"></i> <?= Yii::t('WikiModule.base', 'New page'); ?>
                            </a>
                        </li>

                        <li class="nav-divider"></li>
                    <?php endif; ?>

                    <?php if ($homePage !== null) : ?>
                        <li><?= Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('/wiki/page/index', array())); ?></li>
                    <?php endif; ?>


                    <?php if ($hasCategories): ?>
                        <li><?= Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Categories'), $contentContainer->createUrl('/wiki/overview/list-categories', array())); ?></li>
                    <?php endif ?>
                </ul>
            </div>
        </div>


    </div>
</div>