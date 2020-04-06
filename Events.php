<?php

namespace humhub\modules\wiki;

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
                $event->sender->addItem([
                    'label' => $label,
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
                $event->sender->addItem([
                    'label' => Yii::t('WikiModule.base', 'Wiki'),
                    'url' => $user->createUrl('//wiki/page'),
                    'icon' => '<i class="fa fa-book"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'wiki'),
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }
}
