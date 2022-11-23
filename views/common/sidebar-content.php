<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\widgets\WikiSidebar;

/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $content string */
/* @var $hideSidebarOnSmallScreen bool */

Assets::register($this);
?>
<div class="row">
    <?= WikiSidebar::widget([
        'contentContainer' => $contentContainer,
        'hideOnSmallScreen' => $hideSidebarOnSmallScreen,
    ]) ?>
    <div class="wiki-right-part col-lg-8 <?= $hideSidebarOnSmallScreen ? 'col-md-12' : 'visible-lg' ?>">
        <?= $content ?>
    </div>
</div>