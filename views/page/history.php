<?php

use yii\helpers\Html;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\user\widgets\Image;
use humhub\widgets\LinkPager;
use humhub\modules\wiki\widgets\WikiMenu;

humhub\modules\wiki\assets\Assets::register($this);

/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $pagination \yii\data\Pagination */
/* @var $revisions \humhub\modules\wiki\models\WikiPageRevision[] */


?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">

            <?php WikiContent::begin(['title' => Yii::t('WikiModule.base', '<strong>Page</strong> history')])?>

                <h1 class="wiki-page-history-title"><i class="fa fa-file-text-o"></i> <?= Html::encode($page->title); ?></h1>

                <ul class="wiki-page-history">
                    <?php $first = true; ?>
                    <?php foreach ($revisions as $revision): ?>
                        <li>
                            <div class="media <?= ($first && $pagination->page == 0) ? 'alert alert-warning' : '' ?>">
                                <div class="horizontal-line">---</div>

                                <?= Image::widget(['user' => $revision->author, 'showTooltip' => true, 'width' => 36, 'htmlOptions' => ['class' => 'pull-left'] ]) ?>

                                <div class="media-body">
                                    <i class="fa fa-clock-o history pull-left"></i>
                                    <h4 class="media-heading">
                                        <a href="<?= $contentContainer->createUrl('view', ['title' => $page->title, 'revision' => $revision->revision]); ?>">
                                            <?= Html::encode($page->title); ?></a>
                                           <a class="wiki-page-view-link colorInfo" href="<?= Url::toWiki($page, $revision); ?>">
                                               [ <i class="fa fa-eye"></i><?= Yii::t('WikiModule.base', 'View'); ?> ]
                                           </a><br>
                                        <h5>
                                            <?= Yii::t('WikiModule.base', 'Edited at'); ?>
                                            <?= Yii::$app->formatter->asDateTime($revision->revision); ?>
                                            <?= Yii::t('WikiModule.base', 'by'); ?>
                                            <?= Html::a(Html::encode($revision->author->displayName), $revision->author->getUrl(), ['class' => 'wiki-author-link']); ?>
                                        </h5>
                                    </h4>

                                </div>

                            </div>
                        </li>
                        <?php $first = false; ?>
                    <?php endforeach; ?>

                    <div class="text-center">
                        <?= LinkPager::widget(['pagination' => $pagination]); ?>
                    </div>
                </ul>
            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $page, 'blocks' => [[WikiMenu::LINK_BACK_TO_PAGE], WikiMenu::BLOCK_START]])?>
        </div>
    </div>
</div>
