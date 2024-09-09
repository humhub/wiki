<?php

use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\Button;

/* @var $wiki WikiPage */
/* @var $content string */

Assets::register($this);
?>
<div>
    <div class="wiki-preview">
        <div class="wiki-preview-content" data-ui-markdown>
            <?= empty($content)
                ? Yii::t('WikiModule.base', 'This page is empty.')
                : $content ?>
        </div>

        <?= Button::asLink(Yii::t('UiModule.base', 'Read more'), Url::toWiki($wiki)) ?>
    </div>

</div>
