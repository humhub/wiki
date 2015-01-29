<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.4/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>


<div class="col-md-8">

    <div class="panel panel-default">
        <div class="panel-body">

            <h1><?php echo $page->title; ?></h1>

            <?php
            
            $parser = new \cebe\markdown\MarkdownExtra();
            $md =  $parser->parse($revision->content);
            $purifier = new CHtmlPurifier();
            echo $purifier->purify($md);
                        

            //$md = new CMarkdown;
            //echo $md->transform(CHtml::encode($revision->content));
            ?>

        </div>

    </div>
</div>

<div class="col-md-2">

    <?php if ($revision->is_latest): ?>

        <div class="panel panel-default">
            <div class="panel-body">

                <?php if (!$page->admin_only || $page->canAdminister()) : ?>
                    <?php echo CHtml::link(Yii::t('WikiModule.base', 'Edit'), $this->createContainerUrl('edit', array('id' => $page->id)), array('class' => 'btn btn-primary')); ?>
                    <br />
                    <br />
                <?php endif; ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Show History'), $this->createContainerUrl('history', array('id' => $page->id)), array('class' => 'btn btn-xs btn-primary')); ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <?php $this->widget('application.modules_core.comment.widgets.CommentLinkWidget', array('object' => $page, 'mode' => 'popup')); ?><br />
                <?php $this->widget('application.modules_core.like.widgets.LikeLinkWidget', array('object' => $page)); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php if (!$page->admin_only || $page->canAdminister()) : ?>
                    <?php echo HHtml::postLink(Yii::t('WikiModule.base', 'Revert to this'), $this->createContainerUrl('revert', array('id' => $page->id, 'toRevision' => $revision->revision)), array('class' => 'btn btn-s btn-danger', 'confirm' => Yii::t('WikiModule.base', 'Really sure?'))); ?>
                    <br /><br />
                <?php endif; ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Show latest version'), $this->createContainerUrl('view', array('title' => $page->title)), array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Back to History'), $this->createContainerUrl('history', array('id' => $page->id)), array('class' => 'btn btn-xs btn-primary')); ?>
            </div>
        </div>
    <?php endif; ?>


    <?php if (!$page->isNewRecord): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Go to homepage'), $this->createContainerUrl('//wiki/page/index', array()), array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'List all pages'), $this->createContainerUrl('//wiki/page/list', array()), array('class' => 'btn btn-xs btn-primary')); ?>
                <?php echo CHtml::link(Yii::t('WikiModule.base', 'Create new page'), $this->createContainerUrl('//wiki/page/edit', array()), array('class' => 'btn btn-xs btn-primary')); ?>
            </div>
        </div>

    <?php endif; ?>

</div>