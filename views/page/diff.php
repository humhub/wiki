<?php

use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiRichText;
use yii\helpers\Html;

/* @var $this View */
/* @var $page WikiPage */
/* @var $revision1 WikiPageRevision */
/* @var $revision2 WikiPageRevision */

humhub\modules\wiki\assets\Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <?php WikiContent::begin(['cssClass' => 'wiki-page-content wiki-page-diff']) ?>

            <?= $this->render('_view_header', ['page' => $page]) ?>

            <div class="row">
                <div class="col-xs-6">
                    <strong>
                        <?php if ($revision1->isCurrentlyEditing) : ?>
                            <?= Yii::t('WikiModule.base', 'Your current version'); ?>
                        <?php else : ?>
                            <?= Yii::t('WikiModule.base', 'Edited at'); ?>
                            <?= Yii::$app->formatter->asDateTime($revision1->revision); ?>
                            <?= Yii::t('WikiModule.base', 'by'); ?>
                            <?= Html::a(Html::encode($revision1->author->displayName), $revision1->author->getUrl(), ['class' => 'wiki-author-link']); ?>
                        <?php endif; ?>
                    </strong>
                </div>
                <div class="col-xs-6">
                    <strong>
                        <?php if ($revision2->isCurrentlyEditing) : ?>
                            <?= Yii::t('WikiModule.base', 'Your current version'); ?>
                        <?php else : ?>
                            <?= Yii::t('WikiModule.base', 'Edited at'); ?>
                            <?= Yii::$app->formatter->asDateTime($revision2->revision); ?>
                            <?= Yii::t('WikiModule.base', 'by'); ?>
                            <?= Html::a(Html::encode($revision2->author->displayName), $revision2->author->getUrl(), ['class' => 'wiki-author-link']); ?>
                        <?php endif; ?>
                    </strong>
                </div>
            </div>

            <hr class="wiki-headline-seperator">

            <div class="row">
                <div class="col-xs-6 wiki-page-revision1">
                    <div id="wiki-page-revision1" class="markdown-render" data-ui-widget="wiki.Page" data-ui-init="1" style="display:none">
                        <?= WikiRichText::output($revision1->content, ['id' => 'wiki-page-richtext']) ?>
                    </div>
                </div>
                <div class="col-xs-6 wiki-page-revision2">
                    <div class="markdown-render" data-ui-widget="wiki.Page" data-ui-init="1" data-diff="#wiki-page-revision1" style="display:none">
                        <?= WikiRichText::output($revision2->content, ['id' => 'wiki-page-richtext']) ?>
                    </div>
                </div>
            </div>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $page]) ?>
        </div>
    </div>
</div>
