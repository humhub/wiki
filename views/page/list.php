<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">

                <?php if (count($pages) != 0) : ?>
                    <h1><?php echo Yii::t('WikiModule.base', '<strong>Overview</strong> of all pages'); ?></h1>
                <?php endif; ?>

                <?php if (count($pages) == 0) : ?>
                    <div class="text-center wiki-welcome">
                        <h1><?php echo Yii::t('WikiModule.base', '<strong>Wiki</strong> Module'); ?></h1>
                        <h2><?php echo Yii::t('WikiModule.base', 'No pages created yet.  So it\'s on you.<br>Create the first page now.'); ?></h2>
                        <br>
                        <p>
                            <a href="<?php echo $this->createContainerUrl('//wiki/page/edit'); ?>"
                               class="btn btn-primary btn-lg"><?php echo Yii::t('WikiModule.base', 'Let\'s go!'); ?></a>
                        </p>
                    </div>
                <?php endif; ?>
                <br>
                <ul class="wiki-list">
                    <?php foreach ($pages as $page): ?>
                        <li>
                            <h1 class="wiki-page-history-title"><?php echo HHtml::link('<i class="fa fa-file-text-o"></i> ' . CHtml::encode($page->title), $this->createContainerUrl('view', array('title' => $page->title))); ?></h1>
                        </li>

                    <?php endforeach; ?>
                </ul>

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

            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">

                <ul class="nav nav-pills nav-stacked">
                    <li><a href="<?php echo $this->createContainerUrl('//wiki/page/edit'); ?>"><i
                                class="fa fa-file-text-o new"></i> <?php echo Yii::t('WikiModule.base', 'New page'); ?>
                        </a></li>
                    <?php if (count($pages) != 0) : ?>
                        <li class="nav-divider"></li>
                            <?php if ($homePage !== null) : ?>
                            <li><?php echo CHtml::link('<i class="fa fa-newspaper-o"></i> ' . Yii::t('WikiModule.base', 'Main page'), $this->createContainerUrl('//wiki/page/index', array())); ?></li>
                        <?php endif; ?>
                        <li><?php echo CHtml::link('<i class="fa fa-list-alt"></i> ' . Yii::t('WikiModule.base', 'Overview'), $this->createContainerUrl('//wiki/page/list', array())); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>


    </div>
</div>