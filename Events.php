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
        // Get label from config
        $module = Yii::$app->getModule('wiki');
        $label = $module->settings->space()->get(
            DefaultSettings::SETTING_MODULE_LABEL,
            Yii::t('WikiModule.base', 'Wiki')
        );


        if ($event->sender->space !== null && $event->sender->space->isModuleEnabled('wiki')) {
            $event->sender->addItem([
                'label' => $label,
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
                'url' => $user->createUrl('//wiki/page'),
                'icon' => '<i class="fa fa-book"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'wiki'),
            ]);
        }
    }
}
