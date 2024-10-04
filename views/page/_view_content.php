<?php

use humhub\libs\Html;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicLabel;
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $canEdit bool */
/* @var $content string */
?>
<div class="topic-label-list">
<?php foreach ($page->content->getTags(Topic::class)->all() as $topic) : ?>
    <?= TopicLabel::forTopic($topic) ?>
<?php endforeach; ?>
</div>

<h1 class="wiki-page-title"><?= Html::encode($page->title) ?></h1>

<?= $this->render('_view_category_index', ['page' => $page]) ?>

<?php if (!empty($content)) : ?>
    <div class="markdown-render" data-ui-widget="wiki.Page"<?= $canEdit ? ' data-edit-url="' . Url::toWikiEdit($page) . '"' : '' ?> data-ui-init style="display:none">
        <?= WikiRichText::output($content, ['id' => 'wiki-page-richtext']) ?>
    </div>
<?php else: ?>
    <br>
    <?= Yii::t('WikiModule.base', 'This page is empty.')?><br><br>
    <?= Button::info(Yii::t('WikiModule.base', 'Edit page'))->link(Url::toWikiEdit($page))->icon('fa-pencil-square-o')->visible($canEdit) ?>
<?php endif; ?>
