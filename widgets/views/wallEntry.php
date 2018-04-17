<?php

use humhub\libs\Helpers;
use humhub\widgets\MarkdownView;

?>
<div class="media meeting">
    <div class="media-body">
        <h4 class="media-heading"><a href="<?php echo $wiki->getUrl(); ?>"><?php echo $wiki->title; ?></a></h4>
        <div class="markdown-render">
            <?php echo MarkdownView::widget(['markdown' => Helpers::truncateText($content, 500), 'parserClass' => "humhub\modules\wiki\Markdown"]); ?>
        </div>

        <a href="<?php echo $wiki->getUrl(); ?>" class="btn btn-sm btn-default" data-ui-loader><i class=" fa
                                                                                   fa-file-text-o"></i> <?php echo Yii::t('WikiModule.widgets_views_wallentry', 'Open wiki page...'); ?>
        </a>
    </div>
</div>
