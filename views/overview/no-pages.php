<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\View;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\bootstrap\Button;

/* @var $this View */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $canCreatePage boolean */

humhub\modules\wiki\assets\Assets::register($this);
?>
<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <?php WikiContent::begin() ?>

                <div class="text-center wiki-welcome">
                    <h1><?= Yii::t('WikiModule.base', 'There are no entries yet :(') ?></h1>
                    <?php if ($canCreatePage): ?>
                        <h2><?= Yii::t('WikiModule.base', 'Get your very own knowledge base off the ground by being the first one to create a Wiki page! Gather information, facilitate knowledge transfer and make it available to your users in the easiest way possible.') ?></h2>
                        <br>
                        <p>
                            <?= Button::accent( Yii::t('WikiModule.base', 'Let\'s go!'))->link(Url::toWikiCreate($contentContainer)) ?>
                        </p>
                    <?php endif; ?>
                </div>

            <?php WikiContent::end() ?>

        </div>
    </div>
</div>
