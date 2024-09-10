<?php

use humhub\libs\Html;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\widgets\TopicLabel;
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\modules\wiki\widgets\WikiRichTextNumberingExtension;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $canEdit bool */
/* @var $content string */
// Function to add numbering to headers
function addNumberingToHeaders($content) {
    $headerCounters = [0, 0, 0, 0, 0, 0]; // To keep track of header levels

    // Regex to match markdown headers (e.g., #, ##, ###)
    return preg_replace_callback('/^(#+)\s*(.+)/m', function ($matches) use (&$headerCounters) {
        $headerLevel = strlen($matches[1]);

        // Reset counters for deeper levels
        for ($i = $headerLevel; $i < 6; $i++) {
            $headerCounters[$i] = 0;
        }

        // Increment the counter for the current header level
        $headerCounters[$headerLevel - 1]++;

        // Build the numbering string (e.g., 1.2.3)
        $numbering = '';
        for ($i = 0; $i < $headerLevel; $i++) {
            if ($headerCounters[$i] > 0) {
                $numbering .= $headerCounters[$i] . '.';
            }
        }

        // Return the header with the numbering added
        return $matches[1] . ' ' . trim($numbering, '.') . ' ' . $matches[2];
    }, $content);
}

// Apply header numbering to the content before rendering it
if (!empty($content)) {
    $content = addNumberingToHeaders($content);
}
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
