<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\widgets\WikiSidebar;

/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $content string */
/* @var $hideSidebarOnSmallScreen bool */

Assets::register($this);

$resizableCacheKey = 'wiki.sidebar';
?>
<div class="row">
    <?= WikiSidebar::widget([
        'contentContainer' => $contentContainer,
        'hideOnSmallScreen' => $hideSidebarOnSmallScreen,
        'resizableCacheKey' => $resizableCacheKey,
    ]) ?>
    <div class="wiki-right-part col-lg-8 <?= $hideSidebarOnSmallScreen ? 'col-md-12' : 'visible-lg' ?>">
        <?= $content ?>
    </div>
</div>
<script <?= Html::nonce() ?>>
var cache = localStorage.getItem('<?= $resizableCacheKey ?>');
cache = cache ? JSON.parse(cache) : {};
if (cache && cache.hasOwnProperty('<?= $contentContainer->id ?>')) {
    var sidebarWidth = cache['<?= $contentContainer->id ?>'];
    $('.wiki-page-sidebar').css('width', sidebarWidth + '%');
    $('.wiki-right-part').css('width', (100 - sidebarWidth) + '%');
}
</script>