<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\helpers\Url;
use humhub\widgets\Button;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreate bool */
?>
<div class="panel panel-default wiki-last-edited-pages">
    <div class="panel-body">
        <?= Button::info(Yii::t('WikiModule.base', 'Create page'))->icon('fa-plus')
            ->link(Url::toWikiCreate($contentContainer))->visible($canCreate)
            ->right()->sm()->style('margin:10px 20px') ?>
        <h3><?= Icon::get('search') ?> <?= Yii::t('WikiModule.base', 'Search') ?></h3>
    </div>
</div>