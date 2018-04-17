<?php

namespace humhub\modules\wiki;

use Yii;

/**
 * Description of WikiEvents
 *
 * @author luke
 */
class Events
{

    public static function onSpaceMenuInit($event)
    {
        if ($event->sender->space !== null && $event->sender->space->isModuleEnabled('wiki') && $event->sender->space->isMember()) {
            $event->sender->addItem([
                'label' => Yii::t('WikiModule.base', 'Wiki'),
                'group' => 'modules',
                'url' => $event->sender->space->createUrl('//wiki/page'),
                'icon' => '<i class="fa fa-book"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'wiki'),
            ]);
        }
    }

    public static function onProfileMenuInit($event)
    {
        $user = $event->sender->user;
        if ($user->isModuleEnabled('wiki')) {
            $event->sender->addItem([
                'label' => Yii::t('WikiModule.base', 'Wiki'),
                'group' => 'modules',
                'url' => $user->createUrl('//wiki/page'),
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'wiki'),
            ]);
        }
    }

}
