<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\helpers\Html;
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
            <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php foreach ($entries as $entry) : ?>
                    <li><?= $entry->render(['class' => 'dropdown-item']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?= Html::endTag('div')?>
    <?php endif; ?>
</div>
