<?php

/* @var $blocks [][] */
/* @var $context \humhub\modules\wiki\widgets\WikiMenu */
/* @var $cols int */
?>

<div class="col-lg-<?= $cols ?> col-md-<?= $cols ?> col-sm-<?= $cols ?> wiki-menu">
    <div class="wiki-menu-fixed">
        <ul class="nav nav-pills nav-stacked" data-action-component="content.Content">
            <?= $firstBlockRendered = false ?>
            <?php foreach ($blocks as $blockIndex => $block) : ?>
               <?= $firstLinkRendered = false ?>
                <?php foreach ($block as $linkIndex => $link) : ?>
                    <?php $link = $this->context->renderLink($link) ?>

                    <?php if($firstBlockRendered && !empty($link) && !$firstLinkRendered) : ?>
                        <li class="nav-divider"></li>
                    <?php endif; ?>

                    <?php if(!empty($link)) : ?>
                        <?= $link ?>
                        <?php $firstBlockRendered = $firstLinkRendered = true ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
