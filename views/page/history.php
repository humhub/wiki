<div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-body">

            <h1>Change history of <?php echo CHtml::encode($page->title); ?></h1>

            <ul>
                <?php foreach ($page->revisions as $revision): ?>
                    <li><?php echo HHtml::link(Yii::app()->dateFormatter->formatDateTime($revision->revision), $this->createContainerUrl('view', array('title' => $page->title, 'revision'=>$revision->revision))); ?></li>
                <?php endforeach; ?>
            </ul>

        </div>

    </div>
</div>