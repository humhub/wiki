<?php
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\widgets\WikiContent;

/* @var $this View */
/* @var $page WikiPage */
/* @var $revision WikiPageRevision */
/* @var $content string */
/* @var $canEdit bool */

Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <?php WikiContent::begin(['cssClass' => 'wiki-page-content']) ?>

                <?= $this->render('_view_body', [
                    'page' => $page,
                    'revision' => $revision,
                    'canEdit' => $canEdit,
                    'content' => $content,
                ]) ?>

            <?php WikiContent::end() ?>
        </div>
    </div>
</div>