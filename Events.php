<?php

namespace humhub\modules\wiki;

use humhub\libs\Html;
use Yii;
use humhub\modules\wiki\models\DefaultSettings;

/**
 * Description of WikiEvents
 *
 * @author luke
 */
class Events
{

    public static function onSpaceMenuInit($event)
    {
        try {
            if ($event->sender->space !== null && $event->sender->space->isModuleEnabled('wiki')) {
                $settings = new DefaultSettings(['contentContainer' => $event->sender->space]);
                $event->sender->addItem([
                    'label' => Html::encode($settings->module_label),
                    'group' => 'modules',
                    'url' => $event->sender->space->createUrl('//wiki/page'),
                    'icon' => '<i class="fa fa-book"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'wiki'),
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onProfileMenuInit($event)
    {
        try {
            $user = $event->sender->user;
            if ($user->isModuleEnabled('wiki')) {
                $settings = new DefaultSettings(['contentContainer' => $user]);
                $event->sender->addItem([
                    'label' => substr(Html::encode($settings->module_label), 0, 20),
                    'url' => $user->createUrl('//wiki/page'),
                    'icon' => '<i class="fa fa-book"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'wiki'),
                ]);
            }
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
