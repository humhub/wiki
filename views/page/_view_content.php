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

<!-- CSS styling for numbering of content -->
<style>
    /* Initializing all the counters for headers h1 h2 h3 */
    #numbered { counter-reset: h1 h2 h3;}
    /* Resetting counter for h2 h3 when <h1> occurs */
    #numbered h1{counter-set: h2 0;}
    /* Incrementing h1 counter when <h1> occurs and added it to the content */
    #numbered h1::before { counter-increment: h1; content: counter(h1) ' ';}
    /* Resetting counter for h3 when <h2> occurs */
    #numbered h2{counter-set: h3 0;}
    /* Incrementing h2 counter when <h2> occurs and added it to the content */
    #numbered h2::before { counter-increment: h2; content: counter(h1) '.' counter(h2) ' ';}
    /* Incrementing h3 counter when <h3> occurs and added it to the content */
    #numbered h3::before { counter-increment: h3; content: counter(h1) '.' counter(h2) '.' counter(h3) ' ';}
    /* Ignoring the title of the page as it is also an h1 tag creating it in the inner div class caused issues with counter */
    #numbered h1.wiki-page-title::before { counter-increment: none; content: none;}
</style>


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
