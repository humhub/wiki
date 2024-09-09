<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\ui\menu\MenuEntry;
use humhub\modules\wiki\widgets\WikiMenu;

/* @var $menu WikiMenu */
/* @var $entries MenuEntry[] */
/* @var $options array */
?>
<div class="wiki-menu">
    <?php foreach ($menu->buttons as $button) : ?>
        <?= $menu->renderButton($button) ?>
    <?php endforeach; ?>

    <?php if (!empty($entries)) : ?>
        <?= Html::beginTag('div', $options) ?>
            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
                <?php foreach ($entries as $entry) : ?>
                    <li><?= $entry->render() ?></li>
                <?php endforeach; ?>
            </ul>
        <?= Html::endTag('div')?>
    <?php endif; ?>
</div>
