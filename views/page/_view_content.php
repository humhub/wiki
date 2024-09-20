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
    /* Initializing a counter for h1 */
    #numbered {
        counter-reset: h1count;
    }

    /* Initializing  counter for h2 and resetting when <h1> appears*/
    #numbered h1{
        counter-reset: h2count;
    }

    /* Initializing  counter for h3 and resetting when <h2> appears*/
    #numbered h2{
        counter-reset: h3count;
    }

    /* Incrementing h1 counter when <h1> occurs and added it to the content */
    #numbered h1::before {
        counter-increment: h1count;
        content: counter(h1count) ' ';
    }

    /* Incrementing h2 counter when <h2> occurs and added it to the content */
    #numbered h2::before {
        counter-increment: h2count;
        content: counter(h1count) '.' counter(h2count) ' ';
    }

    /* Incrementing h3 counter when <h3> occurs and added it to the content */
    #numbered h3::before {
        counter-increment: h3count;
        content: counter(h1count) '.' counter(h2count) '.' counter(h3count) ' ';
    }

    /* Ignoring the title of the page as it is also an h1 tag creating it in the inner div class caused issues with counter */
    #numbered h1.wiki-page-title::before {
        counter-increment: none;
        content: none;
    }
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
