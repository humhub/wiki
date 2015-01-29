<div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-body">

            <h1><?php echo Yii::t('WikiModule.base', 'Change history for page: %pageTitle%', array('%pageTitle%' => CHtml::encode($page->title))); ?></h1>

            <ul>
                <?php foreach ($page->revisions as $revision): ?>
                    <li>
                        <?php echo HHtml::link(Yii::app()->dateFormatter->formatDateTime($revision->revision), $this->createContainerUrl('view', array('title' => $page->title, 'revision' => $revision->revision))); ?>
                        by <?php echo CHtml::link(CHtml::encode($revision->author->displayName), $revision->author->getUrl()); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>

    </div>
</div>