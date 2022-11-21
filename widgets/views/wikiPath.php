<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Link;

/* @var $page WikiPage */
/* @var $path WikiPage[] */
?>
<div class="wiki-page-path">
    <?= Link::to(Yii::t('WikiModule.base', 'Index'), Url::toHome($page->content->container))->icon('home')->id('wiki_index') ?>
    <?php foreach ($path as $categoryPage) : ?>
        / <?= Link::to($categoryPage->title, Url::toWiki($categoryPage)) ?>
    <?php endforeach; ?>
</div>