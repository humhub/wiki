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
/* @var $path WikiPage[]|string[] */
?>
<div class="wiki-page-path">
    <?= Link::to('', Url::toHome($page->content->container))->icon('home')->id('wiki_index') ?>
    <?php foreach ($path as $categoryPage) : ?>
        / <?= $categoryPage instanceof WikiPage
            ? Link::to($categoryPage->title, Url::toWiki($categoryPage))
            : '<span>' . $categoryPage . '</span>' ?>
    <?php endforeach; ?>
</div>