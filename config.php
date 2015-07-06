<?php

use module\wiki\Module;
use module\wiki\Events;
use humhub\modules\space\widgets\Menu;
use humhub\modules\user\widgets\ProfileMenu;

return [
    'id' => 'wiki',
    'class' => Module::className(),
    'events' => array(
        array('class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => array(Events::className(), 'onSpaceMenuInit')),
        array('class' => ProfileMenu::className(), 'event' => ProfileMenu::EVENT_INIT, 'callback' => array(Events::className(), 'onProfileMenuInit')),
    ),
];
?>