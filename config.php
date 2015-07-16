<?php

use humhub\modules\wiki\Module;
use humhub\modules\wiki\Events;
use humhub\modules\space\widgets\Menu;
use humhub\modules\user\widgets\ProfileMenu;

return [
    'id' => 'wiki',
    'class' => 'humhub\modules\wiki\Module',
    'namespace' => 'humhub\modules\wiki',
    'events' => array(
        array('class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => array('humhub\modules\wiki\Events', 'onSpaceMenuInit')),
        array('class' => ProfileMenu::className(), 'event' => ProfileMenu::EVENT_INIT, 'callback' => array('humhub\modules\wiki\Events', 'onProfileMenuInit')),
    ),
];
?>