<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Helpers;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Link;

/* @var $page WikiPage */
/* @var $path WikiPage[]|string[] */

$pathLength = count($path);
?>
<div class="wiki-page-path wiki-page-path-length-<?= $pathLength ?>">
    <?= Link::to('', Url::toHome($page->content->container))->icon('home')->id('wiki_index') ?>
    <span class="wiki-page-path-categories">
    <?php foreach ($path as $i => $categoryPage) : ?>

        <?php $isLast = $i === $pathLength - 1 ?>
        <?php if ($i === 0 && $pathLength > 1) : ?>
            <span class="wiki-page-path-first-categories">
        <?php elseif ($isLast) : ?>
            <?php if ($pathLength > 1) : ?></span><?php endif; ?>
            <span class="wiki-page-path-last-category">
        <?php endif; ?>

        / <?= $categoryPage instanceof WikiPage
            ? Link::to($isLast
                    ? $categoryPage->title
                    : Helpers::truncateText($categoryPage->title, 25),
                Url::toWiki($categoryPage))
            : '<span>' . $categoryPage . '</span>' ?>

        <?php if ($isLast) : ?></span><?php endif; ?>

    <?php endforeach; ?>
    </span>
</div>
