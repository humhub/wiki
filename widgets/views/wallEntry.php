<?php

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\converter\RichTextToShortTextConverter;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\widgets\Button;
use humhub\widgets\Link;

/* @var $wiki \humhub\modules\wiki\models\WikiPage */
/* @var $content string */

Assets::register($this);

$wikiUrl = Url::toWiki($wiki);

?>
<div>
    <div class="wiki-preview">
        <div class="wiki-preview-content">
            <?php if(!empty($content)) : ?>
                <?= nl2br(RichTextToShortTextConverter::process($content, [
                    'preserveNewlines' => true,
                    'maxLength' => 500,
                ])) ?>
            <?php else: ?>
                <?= Yii::t('WikiModule.base', 'This page is empty.') ?>
            <?php endif; ?>
        </div>

        <?= Button::asLink(Yii::t('UiModule.base', 'Read more'), $wikiUrl) ?>
    </div>

</div>
