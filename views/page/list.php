<div class="col-md-8">
    <div class="panel panel-default">
        <div class="panel-body">

            <h1><?php echo Yii::t('WikiModule.base', 'List of pages'); ?></h1>

            <?php if (count($pages) == 0) : ?>
                <p><?php echo Yii::t('WikiModule.base', 'No pages created yet!'); ?>
                <?php endif; ?>

            <ul>
                <?php foreach ($pages as $page): ?>
                    <li><?php echo HHtml::link($page->title, $this->createContainerUrl('view', array('title' => $page->title))); ?></li>
                <?php endforeach; ?>
            </ul>

        </div>

    </div>
</div>


<div class="col-md-2">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo CHtml::link(Yii::t('WikiModule.base', 'Create new page'), $this->createContainerUrl('//wiki/page/edit', array()), array('class' => 'btn btn-xs btn-primary')); ?>
        </div>
    </div>

</diV>