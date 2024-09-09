<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiPath;
use humhub\widgets\Button;
use humhub\widgets\Link;
use humhub\widgets\LinkPager;
use humhub\widgets\TimeAgo;
use yii\helpers\Html;

/* @var $this View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $pagination \yii\data\Pagination */
/* @var $revisions \humhub\modules\wiki\models\WikiPageRevision[] */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $isEnabledDiffTool bool */

humhub\modules\wiki\assets\Assets::register($this);

if ($isEnabledDiffTool) {
    $this->registerJsConfig([
        'wiki.History' => [
            'wikiDiffUrl' => Url::toWikiDiff($page),
        ],
    ]);
}
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">

            <?php WikiContent::begin(['cssClass' => 'wiki-page-content']) ?>

            <div class="wiki-headline">
                <div class="wiki-headline-top">
                    <?= WikiPath::widget(['page' => $page]) ?>
                    <?= WikiMenu::widget([
                        'object' => $page,
                        'buttons' => WikiMenu::LINK_BACK_TO_PAGE,
                        'blocks' => [[WikiMenu::LINK_BACK_TO_PAGE], WikiMenu::BLOCK_START],
                    ]) ?>
                </div>
                <div class="wiki-page-title"><?= Yii::t('WikiModule.base', 'Page history') ?></div>
            </div>

            <h1 class="wiki-page-history-title"><?= Html::encode($page->title) ?></h1>

            <ul class="wiki-page-history<?php if ($isEnabledDiffTool) : ?> wiki-page-history-with-diff<?php endif; ?>"
                data-ui-widget="wiki.History" data-ui-init>
                <?php $first = true; ?>
                <?php foreach ($revisions as $revision): ?>
                    <li>
                        <div class="media">
                            <div class="horizontal-line"><?= $isEnabledDiffTool ? Html::input('radio', 'revision_' . $revision->revision, $revision->revision) : ''; ?></div>

                            <div class="media-body">
                                <h4 class="media-heading">
                                    <a href="<?= $contentContainer->createUrl('view', ['title' => $page->title, 'revision' => $revision->revision]); ?>">
                                        <?= Html::encode($page->title); ?>
                                    </a>
                                </h4>
                                <div class="wiki-page-list-row-details">
                                    <?= TimeAgo::widget(['timestamp' => $revision->revision]) ?>
                                    <?php if ($revision->author): ?>
                                        &middot; <?= \humhub\libs\Html::containerLink($revision->author) ?>
                                    <?php endif; ?>
                                    &middot; <?= Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWiki($page, $revision))->cssClass('wiki-page-view-link') ?>
                                </div>

                            </div>

                        </div>
                    </li>
                    <?php $first = false; ?>
                <?php endforeach; ?>

                <div class="text-center">
                    <?= LinkPager::widget(['pagination' => $pagination]); ?>
                </div>
            </ul>

            <?php if ($isEnabledDiffTool) : ?>
                <?= Button::primary(Yii::t('WikiModule.base', 'Compare changes'))
                    ->action('wiki.History.compare')
                    ->cssClass('wiki-page-history-btn-compare') ?>
            <?php endif; ?>

            <?php WikiContent::end() ?>
        </div>
    </div>
</div>
