<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-10 wiki-content">
                <h1><?php echo Yii::t('WikiModule.base', '<strong>Page</strong> history'); ?></h1>
                <br>

                <h1 class="wiki-page-history-title"><i
                        class="fa fa-file-text-o"></i> <?php echo CHtml::encode($page->title); ?></h1>
                <ul class="wiki-page-history">
                    <?php $first = true; ?>
                    <?php foreach ($revisions as $revision): ?>
                        <li>
                            <div class="media <?php if ($first == true && $pagination->currentPage == 0) { echo "alert alert-warning"; $first = false; } ?>">

                                <div class="horizontal-line">---</div>

                                <a href="<?php echo $revision->author->getUrl(); ?>" class="pull-left">
                                    <img class="media-object img-rounded tt"
                                         src="<?php echo $revision->author->getProfileImage()->getUrl(); ?>" alt="36x36"
                                         data-src="holder.js/36x36" style="width: 36px; height: 36px;" width="36"
                                         height="36" data-toggle="tooltip" data-placement="top" title=""
                                         data-original-title="<strong><?php echo CHtml::encode($revision->author->displayName); ?></strong><br><?php echo CHtml::encode($revision->author->profile->title); ?>">
                                </a>

                                <div class="media-body"><i class="fa fa-clock-o history pull-left"></i>
                                    <h4 class="media-heading"><a
                                            href="<?php echo $this->createContainerUrl('view', array('title' => $page->title, 'revision' => $revision->revision)); ?>"><?php echo CHtml::encode($page->title); ?></a> <a class="wiki-page-view-link" href="<?php echo $this->createContainerUrl('view', array('title' => $page->title, 'revision' => $revision->revision)); ?>">[ <i class="fa fa-eye"></i><?php echo Yii::t('WikiModule.base', 'View'); ?> ]</a><br>
                                        <h5><?php echo Yii::t('WikiModule.base', 'Edited at'); ?> <?php echo Yii::app()->dateFormatter->formatDateTime($revision->revision); ?> <?php echo Yii::t('WikiModule.base', 'by'); ?> <?php echo CHtml::link(CHtml::encode($revision->author->displayName), $revision->author->getUrl(), array('class' => 'wiki-author-link')); ?></h5>
                                    </h4>

                                </div>

                            </div>

                        </li>
                    <?php endforeach; ?>

                    <div class="text-center">
                        <?php
                        $this->widget('CLinkPager', array(
                            'pages' => $pagination,
                            'maxButtonCount' => 10,
                            'header' => '',
                            'nextPageLabel' => '<i class="fa fa-step-forward"></i>',
                            'prevPageLabel' => '<i class="fa fa-step-backward"></i>',
                            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
                            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
                            'htmlOptions' => array('class' => 'pagination'),
                        ));
                        ?>
                    </div>
                </ul>
            </div>
            <div class="col-md-2 wiki-menu">
                <ul class="nav nav-pills nav-stacked" style="max-width: 300px;">
                    <li><?php echo CHtml::link('<i class="fa fa-reply back"></i> ' . Yii::t('WikiModule.base', 'Back to page'), $this->createContainerUrl('//wiki/page/view', array('title' => $page->title))); ?></li>
                    <li class="nav-divider"></li>
                    <?php if ($homePage !== null) : ?>
                        <li><?php echo CHtml::link('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $this->createContainerUrl('//wiki/page/index', array())); ?></li>
                    <?php endif; ?>
                    <li><?php echo CHtml::link('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $this->createContainerUrl('//wiki/page/list', array())); ?></li>
                </ul>
            </div>
        </div>


    </div>
</div>
