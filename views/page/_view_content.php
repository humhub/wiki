<?php
use humhub\modules\wiki\widgets\WikiRichText;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */


?>

<?php if (!empty($content)) : ?>
    <div class="markdown-render" style="display:none">
        <?= WikiRichText::output($content, ['id' => 'wiki-page-richtext']) ?>
    </div>
<?php else: ?>
    <br>
    <div class="alert alert-info clearfix">
        <?= Yii::t('WikiModule.base', 'This page does not contain any content yet.')?><br><br>
        <?= Button::primary(Yii::t('WikiModule.base', 'Edit page'))->link(Url::toWikiEdit($page))->sm()->icon('fa-pencil-square-o') ?>
    </div>
<?php endif; ?>
