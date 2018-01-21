<?php

use yii\helpers\Html;

humhub\modules\wiki\Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">
                <h1><?php echo Yii::t('WikiModule.base', '<strong>Page</strong> history'); ?></h1>
                <br>

                <h1 class="wiki-page-history-title"><i
                        class="fa fa-file-text-o"></i> <?php echo Html::encode($page->title); ?></h1>
                <ul class="wiki-page-history">
                    <?php $first = true; ?>
                    <?php foreach ($revisions as $revision) : ?>
                        <li>
                            <div class="media <?php
                            if ($first == true && $pagination->page == 0) {
                                echo 'alert alert-warning';
                                $first = false;
                            }
                        ?>">

                                <div class="horizontal-line">---</div>

                                <a href="<?php echo $revision->author->getUrl(); ?>" class="pull-left">
                                    <img class="media-object img-rounded tt"
                                         src="<?php echo $revision->author->getProfileImage()->getUrl(); ?>" alt="36x36"
                                         data-src="holder.js/36x36" style="width: 36px; height: 36px;" width="36"
                                         height="36" data-toggle="tooltip" data-placement="top" title=""
                                         data-original-title="<?php echo Html::encode($revision->author->displayName); ?>">
                                </a>

                                <div class="media-body"><i class="fa fa-clock-o history pull-left"></i>
                                    <h4 class="media-heading"><a
                                            href="<?php echo $contentContainer->createUrl('view', ['title' => $page->title, 'revision' => $revision->revision]); ?>"><?php echo Html::encode($page->title); ?></a> <a class="wiki-page-view-link colorInfo" href="<?php echo $contentContainer->createUrl('view', ['title' => $page->title, 'revision' => $revision->revision]); ?>">[ <i class="fa fa-eye"></i><?php echo Yii::t('WikiModule.base', 'View'); ?> ]</a><br>
                                        <h5><?php echo Yii::t('WikiModule.base', 'Edited at'); ?> <?php echo Yii::$app->formatter->asDateTime($revision->revision); ?> <?php echo Yii::t('WikiModule.base', 'by'); ?> <?php echo Html::a(Html::encode($revision->author->displayName), $revision->author->getUrl(), ['class' => 'wiki-author-link']); ?></h5>
                                    </h4>

                                </div>

                            </div>

                        </li>
                    <?php endforeach; ?>

                    <div class="text-center">
                        <?= \humhub\widgets\LinkPager::widget(['pagination' => $pagination]); ?> 
                    </div>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">
                <ul class="nav nav-pills nav-stacked">
                    <li><?php echo Html::a('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Back to page'), $contentContainer->createUrl('/wiki/page/view', ['title' => $page->title])); ?></li>
                    <li class="nav-divider"></li>
                        <?php if ($homePage !== null) : ?>
                        <li><?php echo Html::a('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $contentContainer->createUrl('/wiki/page/index', [])); ?></li>
                        <?php endif; ?>
                    <li><?php echo Html::a('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $contentContainer->createUrl('/wiki/page/list', [])); ?></li>
                </ul>
            </div>
        </div>


    </div>
</div>
