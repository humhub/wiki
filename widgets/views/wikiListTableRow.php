<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;

/* @var WikiPage $wikiPage */
?>
<?php if ($wikiPage instanceof WikiPage) : ?>
    <strong class="wiki-page-list-row-title"><?= Link::to($wikiPage->title, Url::toWiki($wikiPage)) ?></strong>
    <div class="wiki-page-list-row-details">
        <?= TimeAgo::widget(['timestamp' => $wikiPage->content->updated_at]) ?>
        <?php if ($wikiPage->content->updatedBy) : ?>
            &middot; <?= Html::encode($wikiPage->content->updatedBy->displayName) ?>
        <?php endif; ?>
        &middot; <?= Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWikiHistory($wikiPage)) ?>
    </div>
<?php endif; ?>
