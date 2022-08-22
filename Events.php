<?php

namespace humhub\modules\wiki;

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\widgets\Menu;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\LeftNavigation;
use humhub\modules\user\widgets\ProfileMenu;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\modules\wiki\models\WikiPage;
use Yii;

/**
 * Description of WikiEvents
 *
 * @author luke
 */
class Events
{

    /**
     * Initialize Space/Profile menu items
     *
     * @param ContentContainerActiveRecord $container
     * @param LeftNavigation $menu
     */
    public static function InitContainerMenus(ContentContainerActiveRecord $container, LeftNavigation $menu)
    {
        if (empty($container) || !$container->moduleManager->isEnabled('wiki')) {
            return;
        }

        $settings = new DefaultSettings(['contentContainer' => $container]);
        $menu->addEntry(new MenuLink([
            'label' => Html::encode($settings->module_label),
            'url' => $container->createUrl('/wiki/page'),
            'icon' => 'book',
            'isActive' => MenuLink::isActiveState('wiki'),
        ]));

        // Display Wiki pages with option "Show in Space/Profile menu"
        $containerMenuWikiPages = WikiPage::find()
            ->contentContainer($container)
            ->readable()
            ->where(['is_container_menu' => 1])
            ->all();
        foreach ($containerMenuWikiPages as $containerMenuWikiPage) {
            /* @var WikiPage $containerMenuWikiPage */
            $menu->addEntry(new MenuLink([
                'label' => Html::encode($containerMenuWikiPage->title),
                'url' => $containerMenuWikiPage->getUrl(),
                'icon' => 'file-text-o',
                'isActive' => MenuLink::isActiveState('wiki', 'page', 'view') && Yii::$app->request->get('title') == $containerMenuWikiPage->title,
                'sortOrder' => $containerMenuWikiPage->container_menu_order,
            ]));
        }
    }

    public static function onSpaceMenuInit($event)
    {
        try {
            /* @var Menu $spaceMenu */
            $spaceMenu = $event->sender;
            self::InitContainerMenus($spaceMenu->space, $spaceMenu);
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onProfileMenuInit($event)
    {
        try {
            /* @var ProfileMenu $profileMenu */
            $profileMenu = $event->sender;
            self::InitContainerMenus($profileMenu->user, $profileMenu);
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onRestApiAddRules()
    {
        /* @var \humhub\modules\rest\Module $restModule */
        $restModule = Yii::$app->getModule('rest');
        $restModule->addRules([

            ['pattern' => 'wiki', 'route' => 'wiki/rest/wiki/find', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'wiki/container/<containerId:\d+>', 'route' => 'wiki/rest/wiki/find-by-container', 'verb' => 'GET'],
            ['pattern' => 'wiki/container/<containerId:\d+>', 'route' => 'wiki/rest/wiki/delete-by-container', 'verb' => 'DELETE'],

            //Wiki Page CRUD
            ['pattern' => 'wiki/container/<containerId:\d+>', 'route' => 'wiki/rest/wiki/create', 'verb' => 'POST'],
            ['pattern' => 'wiki/page/<id:\d+>', 'route' => 'wiki/rest/wiki/view', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'wiki/page/<id:\d+>', 'route' => 'wiki/rest/wiki/update', 'verb' => 'PUT'],
            ['pattern' => 'wiki/page/<id:\d+>', 'route' => 'wiki/rest/wiki/delete', 'verb' => ['DELETE']],

            //Wiki Page Management
            ['pattern' => 'wiki/page/<id:\d+>/change-index', 'route' => 'wiki/rest/wiki/change-index', 'verb' => 'PATCH'],
            ['pattern' => 'wiki/page/<id:\d+>/move', 'route' => 'wiki/rest/wiki/move', 'verb' => 'PATCH'],

            //Wiki Page Revision
            ['pattern' => 'wiki/page/<pageId:\d+>/revisions', 'route' => 'wiki/rest/revision/index', 'verb' => ['GET', 'HEAD']],
            ['pattern' => 'wiki/revision/<id:\d+>', 'route' => 'wiki/rest/revision/view', 'verb' => 'GET'],
            ['pattern' => 'wiki/revision/<id:\d+>/revert', 'route' => 'wiki/rest/revision/revert', 'verb' => 'PATCH'],

        ], 'mail');
    }
}
