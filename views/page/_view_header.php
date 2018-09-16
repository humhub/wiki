<?php
use humhub\libs\Html;
use humhub\widgets\Label;
use humhub\widgets\Link;
use humhub\widgets\TimeAgo;

/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */

$icon = $page->is_category ? 'fa-file-word-o' : 'fa-file-text-o';

if($page->is_home) {
    $icon = 'fa-home';
}

?>

<h1>
    <i class="fa <?= $icon?> "></i>
    <strong><?= Html::encode($page->title); ?></strong>
    <?php if($page->categoryPage) : ?>
        <?= Label::primary(Html::encode($page->categoryPage->title))->withLink(Link::to(null, $page->categoryPage->getUrl()))->right() ?>
    <?php endif; ?>
</h1>

<hr style="margin-bottom:4px">

<div class="wiki-content-info clearfix">
    <small class="pull-right">
        <?= Yii::t('WikiModule.base', 'Last updated '). TimeAgo::widget(['timestamp' => $page->content->updated_at]) ?> by
        <strong>
            <a href="<?= $page->content->updatedBy->getUrl() ?>" style="color:<?= $this->theme->variable('info')?>" data-contentcontainer-id="<?= $page->content->updatedBy->contentcontainer_id ?>">
                <?= Html::encode($page->content->updatedBy->displayName)?>
            </a>
        </strong>
    </small>
</div>
