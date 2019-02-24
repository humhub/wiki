<?php

use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\like\widgets\LikeLink;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiContent;


/* @var $this \humhub\components\View */
/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $revision \humhub\modules\wiki\models\WikiPageRevision */
/* @var $homePage string */
/* @var $content string */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $canViewHistory bool */
/* @var $canEdit bool */
/* @var $canAdminister bool */
/* @var $canCreatePage bool */


humhub\modules\wiki\assets\Assets::register($this);

?>
<div class="panel panel-default">
    <div class="panel-body">

        <div class="row">
            <?php WikiContent::begin(['cssClass' => 'wiki-page-content']) ?>

                <?= $this->render('_view_header', ['page' => $page]) ?>

                <?= $this->render('_view_content', ['page' => $page, 'canEdit' => $canEdit, 'content' => $content]) ?>

                <?= $this->render('_view_category_index', ['page' => $page]) ?>

            <div class="social-controls">
                <?= LikeLink::widget(['object' => $page]); ?>
                &middot; <?= CommentLink::widget(['object' => $page]); ?>
            </div>

            <?= Comments::widget(['object' => $page]); ?>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $page, 'revision' => $revision]) ?>

        </div>
    </div>
</div>
