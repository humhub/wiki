<?php

namespace humhub\modules\wiki;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\wiki\models\WikiPage;
use Yii;

class Module extends ContentContainerModule
{
    /**
     * @var int amount of results per page in the wiki overview
     */
    public $pageSize = 30;

    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::class,
            User::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        return $container->createUrl('/wiki/container-config');
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (WikiPage::find()->all() as $page) {
            $page->delete();
        }

        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return Yii::t('WikiModule.base', 'Adds a wiki to this space.');
        } elseif ($container instanceof User) {
            return Yii::t('WikiModule.base', 'Adds a wiki to your profile.');
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);

        foreach (WikiPage::find()->contentContainer($container)->all() as $page) {
            $page->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function getContainerPermissions($contentContainer = null)
    {
        return [
            new permissions\CreatePage(),
            new permissions\EditPages(),
            new permissions\AdministerPages(),
            new permissions\ViewHistory(),
        ];
    }

}
