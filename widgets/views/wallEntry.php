<?php

use humhub\libs\Html;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\widgets\Button;
use humhub\widgets\Link;

/* @var $wiki \humhub\modules\wiki\models\WikiPage */

\humhub\modules\wiki\assets\Assets::register($this);

$wikiUrl = Url::toWiki($wiki);

?>
<div class="media meeting">
    <div class="media-body">
        <h4 class="media-heading"><?= Link::to(Html::encode($wiki->title), $wikiUrl)->icon('fa-file-text-o')?></h4>
        <br>
        <div class="markdown-render">
            <?= WikiRichText::output($content, ['maxLength' => 500, 'exclude' => ['anchor']]) ?>
        </div>
        <br>
        <?= Button::defaultType(Yii::t('WikiModule.widgets_views_wallentry', 'Open wiki page...'))->link($wikiUrl)->sm() ?>
    </div>
</div>
