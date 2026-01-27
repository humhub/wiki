<?php

use humhub\components\View;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiPath;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;
use humhub\widgets\bootstrap\LinkPager;
use humhub\widgets\TimeAgo;
use yii\data\Pagination;
use yii\helpers\Html;

/* @var $this View */
/* @var $page WikiPage */
/* @var $pagination Pagination */
/* @var $revisions WikiPageRevision[] */
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
                    <div class="horizontal-line"><?= $isEnabledDiffTool ? Html::input('radio', 'revision_' . $revision->revision, $revision->revision) : ''; ?></div>

                    <div class="flex-grow-1">
                        <h4 class="mt-0">
                            <a href="<?= $contentContainer->createUrl('view', ['title' => $page->title, 'revision' => $revision->revision]); ?>">
                                <?= Html::encode($page->title); ?>
                            </a>
                        </h4>
                        <div class="wiki-page-list-row-details">
                            <?= TimeAgo::widget(['timestamp' => $revision->revision]) ?>
                            <?php if ($revision->author): ?>
                                &middot; <?= \humhub\helpers\Html::containerLink($revision->author) ?>
                            <?php endif; ?>
                            &middot; <?= Link::to(Yii::t('WikiModule.base', 'show changes'), Url::toWiki($page, $revision))->cssClass('wiki-page-view-link') ?>
                        </div>

                    </div>
                </li>
                <?php $first = false; ?>
            <?php endforeach; ?>

            <div class="d-flex justify-content-center">
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
