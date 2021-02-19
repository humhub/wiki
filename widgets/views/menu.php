<?php

/* @var $blocks [][] */
/* @var $context \humhub\modules\wiki\widgets\WikiMenu */
/* @var $cols int */
/* @var $options array */

use humhub\libs\Html;
use humhub\widgets\Button; ?>

<?= Html::beginTag('div', $options)?>
    <div class="wiki-collapse-menu clearfix visible-lg visible-md">
        <?= Button::defaultType()->icon('bars')->id('wikiMenuToggle')
        ->action('toggleMenu')
        ->right()->xs()->loader(false) ?>
    </div>
    <div class="wiki-menu-fixed">

        <ul class="nav wiki-menu-main nav-pills nav-stacked">
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
<?= Html::endTag('div')?>
