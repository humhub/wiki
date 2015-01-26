<div class="col-md-8">

    <div class="panel panel-default">
        <div class="panel-body">

            <h1><?php echo $page->title; ?></h1>

            <?php
            $md = new CMarkdown;
            echo $md->transform(CHtml::encode($revision->content));
            ?>

        </div>

    </div>
</div>

<div class="col-md-2">
    <div class="panel panel-default">
        <div class="panel-body">

            <?php echo CHtml::link(Yii::t('WikiModule.base', 'Edit'), $this->createContainerUrl('edit', array('title' => $page->title)), array('class' => 'btn btn-primary')); ?>
            <br />
            <br />
            <?php echo CHtml::link(Yii::t('WikiModule.base', 'Delete'), $this->createContainerUrl('edit', array('title' => $page->title)), array('class' => 'btn btn-xs btn-primary')); ?>
            <?php echo CHtml::link(Yii::t('WikiModule.base', 'Show History'), $this->createContainerUrl('history', array('id' => $page->id)), array('class' => 'btn btn-xs btn-primary')); ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <?php $this->widget('application.modules_core.comment.widgets.CommentLinkWidget', array('object' => $page, 'mode' => 'popup')); ?><br />
            <?php $this->widget('application.modules_core.like.widgets.LikeLinkWidget', array('object' => $page)); ?>

        </div>
    </div>


    <?php if (!$page->isNewRecord): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Go to homepage'), $this->createContainerUrl('//wiki/page/index', array()), array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'List all pages'), $this->createContainerUrl('//wiki/page/list', array()), array('class' => 'btn btn-xs btn-primary')); ?>
            </div>
        </div>

    <?php endif; ?>

</div>