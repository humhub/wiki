<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\helpers\Url;
use humhub\widgets\Button;

/* @var $this \humhub\components\View */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $canCreatePage boolean */

humhub\modules\wiki\assets\Assets::register($this);
?>
<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <?php WikiContent::begin(['cols' => 10])?>

                <div class="text-center wiki-welcome">
                    <h1><?= Yii::t('WikiModule.base', '<strong>Wiki</strong> Module'); ?></h1>
                    <h2><?= Yii::t('WikiModule.base', 'No pages created yet.  So it\'s on you.<br>Create the first page now.'); ?></h2>
                    <?php if ($canCreatePage): ?>
                        <br>
                        <p>
                            <?= Button::primary( Yii::t('WikiModule.base', 'Let\'s go!'))->link(Url::toWikiCreate($contentContainer))?>
                        </p>
                    <?php endif; ?>
                </div>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['cols' => 2, 'container' => $contentContainer, 'excludes' => [WikiMenu::LINK_INDEX]]) ?>

        </div>
    </div>
</div>