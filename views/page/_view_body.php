<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\comment\widgets\Comments;
use humhub\modules\content\widgets\ContentObjectLinks;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\helpers\Url;

/* @var $this View */
/* @var $page WikiPage */
/* @var $revision WikiPageRevision */
/* @var $content string */
/* @var $canEdit bool */

Assets::register($this);
// Determine the current numbering state from the URL parameter
$numberingEnabled = Yii::$app->request->get('numbering', 'disabled') === 'enabled';
?>
<?= $this->render('_view_header', ['page' => $page, 'revision' => $revision, 'displayTitle' => false]) ?>

<!-- Adding ID for the body of the wiki page -->
<div class="wiki-page-body <?php if($numberingEnabled) echo 'numbered';?>">
    <?= $this->render('_view_content', ['page' => $page, 'canEdit' => $canEdit, 'content' => $content]) ?>
</div>

<hr>

<div class="wall-entry-controls social-controls">
    <?= ContentObjectLinks::widget([
        'object' => $page,
        'seperator' => '&middot;',
    ]) ?>
</div>

<?= Comments::widget(['object' => $page]) ?>
