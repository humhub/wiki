<?php

use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicLabel;
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $canEdit bool */


?>

<div class="topic-label-list">
<?php foreach ($page->content->getTags(Topic::class)->all() as $topic) : ?>
    <?= TopicLabel::forTopic($topic) ?>
<?php endforeach; ?>
</div>

<?php if (!empty($content)) : ?>
    <div class="markdown-render" data-ui-widget="wiki.Page"  data-ui-init="1" style="display:none">
        <?= WikiRichText::output($content, ['id' => 'wiki-page-richtext']) ?>
    </div>
<?php else: ?>
    <br>
    <div class="alert alert-info clearfix">
        <?= Yii::t('WikiModule.base', 'This page is empty.')?><br><br>
        <?= Button::primary(Yii::t('WikiModule.base', 'Edit page'))->link(Url::toWikiEdit($page))->sm()->icon('fa-pencil-square-o')->visible($canEdit) ?>
    </div>
<?php endif; ?>
