<?php

use humhub\libs\Html;
use humhub\widgets\Button;
use humhub\modules\wiki\helpers\Url;

/* @var $page \humhub\modules\wiki\models\WikiPage */
/* @var $icon string */
/* @var $title string */
/* @var $url string */
/* @var $showDrag bool */
/* @var $showAddPage bool */
?>

<div class="<?php if (!$page || $page->is_category) : ?>page-category-title<?php else : ?>page-title<?php endif; ?>">
    <?= Button::asLink()->icon('fa-bars')->cssClass('wiki-page-control drag-icon')->visible($page && $showDrag) ?>
    <i class="fa <?= $icon ?>"></i> <?= Html::a(Html::encode($title), $url) ?>

    <?php if ($page && $page->is_category) : ?>
        <?= Button::asLink(null, Url::toWikiCreateForCategory($page))->icon('fa-plus')
            ->cssClass('wiki-page-control tt wiki-category-add')->style('display:none')
            ->title(Yii::t('WikiModule.base', 'Add Page'))->visible($showAddPage) ?>
    <?php endif; ?>
</div>