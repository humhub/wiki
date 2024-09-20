<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\ui\menu\MenuEntry;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\helpers\Url;

/* @var $menu WikiMenu */
/* @var $entries MenuEntry[] */
/* @var $options array */

// Determine the current numbering state from the URL parameter
$numbering_enabled = Yii::$app->request->get('numbering', 'disabled') === 'enabled';

?>
<div class="wiki-menu">
    
    <!-- Add toggle switch with URL-based parameter for numbering-->
    <a href="<?= Url::current(['numbering' => $numbering_enabled ? 'disabled' : 'enabled']) ?>" class="btn-sm btn btn-info">
        <?= $numbering_enabled ? 'Disable Numbering' : 'Enable Numbering' ?>
    </a>

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
