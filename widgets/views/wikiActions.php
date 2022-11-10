<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\widgets\WikiActions;

/* @var $this View */
/* @var $widget WikiActions */
$widget = $this->context;
?>
<div class="wiki-page-actions">
    <?php foreach ($widget->buttons as $button) : ?>
        <?= $widget->renderButton($button) ?>
    <?php endforeach; ?>

    <?php if (!empty($widget->menu)) : ?>
    <div class="btn-group dropdown-navigation">
        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <?php foreach ($widget->menu as $entry) : ?>
                <li><?= $entry->render() ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
