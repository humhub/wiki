<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */

/**
 * Description of WikiEvents
 *
 * @author luke
 */
class WikiModuleEvents
{

    public static function onSpaceMenuInit(CEvent $event)
    {
        if ($event->sender->space->isModuleEnabled('wiki') && $event->sender->space->isMember()) {
            $event->sender->addItem(array(
                'label' => Yii::t('WikiModule.base', 'Wiki'),
                'group' => 'modules',
                'url' => $event->sender->space->createUrl('//wiki/page'),
                'icon' => '<i class="fa fa-book"></i>',
                'isActive' => (Yii::app()->controller->module && Yii::app()->controller->module->id == 'wiki'),
            ));
        }
    }

    public static function onProfileMenuInit($event)
    {
        $user = Yii::app()->getController()->getUser();

        if ($user->isModuleEnabled('wiki')) {
            $event->sender->addItem(array(
                'label' => Yii::t('WikiModule.base', 'Wiki'),
                'group' => 'modules',
                'url' => $user->createUrl('//wiki/page'),
                'isActive' => (Yii::app()->controller->module && Yii::app()->controller->module->id == 'wiki'),
            ));
        }
    }

}
