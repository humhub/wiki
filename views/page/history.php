<div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-body">
            <h1><?php echo Yii::t('WikiModule.base', 'Change history for page: %pageTitle%', array('%pageTitle%' => CHtml::encode($page->title))); ?></h1>
            <ul>
                <?php foreach ($revisions as $revision): ?>
                    <li>
                        <?php echo HHtml::link(Yii::app()->dateFormatter->formatDateTime($revision->revision), $this->createContainerUrl('view', array('title' => $page->title, 'revision' => $revision->revision))); ?>
                        by <?php echo CHtml::link(CHtml::encode($revision->author->displayName), $revision->author->getUrl()); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <center>
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
            </center>            

        </div>
    </div>
</div>
<div class="col-md-2">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo CHtml::link(Yii::t('WikiModule.base', 'Back to page'), $this->createContainerUrl('//wiki/page/view', array('title' => $page->title)), array('class' => 'btn btn-xs btn-primary')); ?>
        </div>
    </div>

</div>