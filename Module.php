<?php

namespace humhub\modules\wiki;

use humhub\components\console\Application as ConsoleApplication;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\modules\wiki\models\WikiPage;
use Yii;
use yii\helpers\Url;

/**
 * @property-read bool $contentHiddenGlobalDefault
 * @property-read bool $hideNavigationEntryDefault
 */
class Module extends ContentContainerModule
{
    /**
     * @var int amount of results per page in the wiki overview
     */
    public $pageSize = 30;

    /**
     * @var int Maximum number of H3 type titles, after which they are hidden in the Table of Contents (TOC)
     */
    public $tocMaxH3 = 20;

    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app instanceof ConsoleApplication) {
            // Prevents the Yii HelpCommand from crawling all web controllers and possibly throwing errors at REST endpoints if the REST module is not available.
            $this->controllerNamespace = 'wiki/commands';
        }
    }

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
    public function getConfigUrl()
    {
        return Url::to(['/wiki/config']);
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
        foreach (WikiPage::find()->each() as $page) {
            $page->hardDelete();
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

        foreach (WikiPage::find()->contentContainer($container)->each() as $page) {
            $page->hardDelete();
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

    /**
     * @inheritdoc
     */
    public function getContentClasses(): array
    {
        return [WikiPage::class];
    }

    public function getContentHiddenGlobalDefault(): bool
    {
        return $this->settings->get('contentHiddenGlobalDefault', false);
    }

    public function getHideNavigationEntryDefault(): bool
    {
        return $this->settings->get('hideNavigationEntryDefault', false);
    }

    public function getContentHiddenDefault(ContentContainerActiveRecord $contentContainer): bool
    {
        return (new DefaultSettings(['contentContainer' => $contentContainer]))->contentHiddenDefault;
    }

}
