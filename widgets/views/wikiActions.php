<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/* @var $blocks [][] */
/* @var $buttons array */
?>

<div class="wiki-page-actions">
    <?php foreach($buttons as $button) : ?>
        <?= $this->context->renderButton($button) ?>
    <?php endforeach; ?>

    <?php foreach ($blocks as $blockIndex => $block) : ?>
        <?php foreach ($block as $linkIndex => $link) : ?>

        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<?php /* Html::beginTag('div', $options)?>
    <div class="wiki-menu-fixed">
        <ul class="nav nav-pills nav-stacked" data-action-component="content.Content">
            <?php $firstBlockRendered = false ?>
            <?php foreach ($blocks as $blockIndex => $block) : ?>
                <?php $firstLinkRendered = false ?>
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
<?= Html::endTag('div') */ ?>
