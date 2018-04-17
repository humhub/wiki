<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/* @var $this \humhub\components\View */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */
/* @var $canCreatePage boolean */
/* @var $createPageUrl string */

humhub\modules\wiki\Assets::register($this);
?>
<div class="panel panel-default wiki-bg">

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9 wiki-content">
                <div class="text-center wiki-welcome">
                    <h1><?= Yii::t('WikiModule.base', '<strong>Wiki</strong> Module'); ?></h1>
                    <h2><?= Yii::t('WikiModule.base', 'No pages created yet.  So it\'s on you.<br>Create the first page now.'); ?></h2>
                    <?php if ($canCreatePage): ?>
                        <br>
                        <p>
                            <a href="<?= $createPageUrl; ?>" data-ui-loader
                               class="btn btn-primary btn-lg"><?php echo Yii::t('WikiModule.base', 'Let\'s go!'); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-3 wiki-menu">
                <ul class="nav nav-pills nav-stacked">
                    <?php if ($canCreatePage): ?>
                        <li>
                            <a href="<?= $createPageUrl; ?>">
                                <i class="fa fa-file-text-o new"></i> <?= Yii::t('WikiModule.base', 'New page'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </div>
</div>