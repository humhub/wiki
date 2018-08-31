<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $pages \humhub\modules\wiki\models\WikiPage[] a list of pages without category */
/* @var $pagination \humhub\modules\wiki\models\WikiPageRevision */
/* @var $homePage boolean */
/* @var $canAdminister boolean */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

humhub\modules\wiki\assets\Assets::register($this);
?>
<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">
                <h1><?= Yii::t('WikiModule.base', '<strong>Overview</strong>'); ?></h1>

                <br>
                <ul class="wiki-page-list">

                    <?php foreach ($categories as $category): ?>
                        <?php $total = $category->findChildren()->count(); ?>
                        <li>
                            <div class="page-category-title" style="margin-bottom:12px">
                                <?= Html::a('<i class="fa fa-list-ol"></i> ' . Html::encode($category->title) . ' (' . $total . ')', $category->getUrl()); ?>
                            </div>
                            <ul class="wiki-page-list">
                                <?php foreach ($category->findChildren()->limit($categoryPageLimit)->all() as $page): ?>
                                    <li>
                                        <span class="page-title"><i
                                                    class="fa fa-file-text-o"></i> <?= Html::a(Html::encode($page->title), $page->getUrl()); ?></span>
                                    </li>
                                <?php endforeach; ?>

                                <?php if ($total > $categoryPageLimit): ?>
                                    <li>
                                        <small>
                                            <i class="fa fa-files-o"></i> <?= Html::a(Yii::t('WikiModule.base', 'Show all {count} pages.', ['count' => $total]), $category->getUrl()); ?>
                                        </small>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <br/>
                        </li>
                    <?php endforeach; ?>

                    <?php $total = $pagesWithoutCategoryQuery->count(); ?>
                    <?php if ($total != 0): ?>
                        <li>
                            <div class="page-category-title" style="margin-bottom:12px">
                                <?= Html::a('<i class="fa fa-list-ol"></i> ' . Yii::t('WikiModule.base', 'Pages without category') . ' (' . $total . ')', $contentContainer->createUrl('/wiki/page/list', [])); ?>
                            </div>
                            <ul class="wiki-page-list">
                                <?php foreach ($pagesWithoutCategoryQuery->limit($categoryPageLimit)->all() as $page): ?>
                                    <li>
                                        <span class="page-title"><i
                                                    class="fa fa-file-text-o"></i> <?= Html::a(Html::encode($page->title), $page->getUrl()); ?></span>
                                    </li>
                                <?php endforeach; ?>

                                <?php if ($total > $categoryPageLimit): ?>
                                    <li>
                                        <small>
                                            <i class="fa fa-files-o"></i> <?= Html::a(Yii::t('WikiModule.base', 'Show all {count} pages.', ['count' => $total]), $contentContainer->createUrl('/wiki/page/list', [])); ?>
                                        </small>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                </ul>

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
                        <li><?= Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('/wiki/page/index', [])); ?></li>
                    <?php endif; ?>

                    <li><?= Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Page list'), $contentContainer->createUrl('/wiki/page/list', [])); ?></li>
                </ul>
            </div>
        </div>


    </div>
</div>