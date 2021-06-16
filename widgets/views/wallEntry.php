<?php

use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\widgets\Button;

/* @var $wiki \humhub\modules\wiki\models\WikiPage */
/* @var $content string */

Assets::register($this);

$wikiUrl = Url::toWiki($wiki);

?>
<div>
    <div class="wiki-preview">
        <div class="wiki-preview-content">
            <?php if(!empty($content)) : ?>
                <?= WikiRichText::output($content, ['maxLength' => 500, 'exclude' => ['anchor']]) ?>
            <?php else: ?>
                <?= Yii::t('WikiModule.base', 'This page is empty.') ?>
            <?php endif; ?>
        </div>

        <?= Button::asLink(Yii::t('UiModule.base', 'Read more'), $wikiUrl) ?>
    </div>

</div>
