<?php

use humhub\modules\comment\widgets\CommentLink;
use humhub\modules\comment\widgets\Comments;
use humhub\modules\like\widgets\LikeLink;


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
            <div class="col-lg-9 col-md-9 col-sm-9 wiki-content wiki-page-content">

                <?= $this->render('_view_header', ['page' => $page])?>

                <?= $this->render('_view_content', ['page' => $page]) ?>

                <?= $this->render('_view_category_index', ['page' => $page]) ?>


                <div class="social-controls">
                    <?= LikeLink::widget(['object' => $page]); ?>
                    &middot; <?= CommentLink::widget(['object' => $page]); ?>
                </div>

                <?= Comments::widget(['object' => $page]); ?>

            </div>

            <div class="col-lg-3 col-md-3 col-sm-3 wiki-menu">
                <?= $this->render('_view_menu', [
                    'page' => $page,
                    'revision' => $revision,
                    'homePage' => $homePage,
                    'canViewHistory' => $canViewHistory,
                    'canEdit' => $canEdit,
                    'canAdminister' => $canAdminister,
                    'canCreatePage' => $canCreatePage,
                ])?>
            </div>
        </div>
    </div>
</div>
