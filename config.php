<?php

use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\space\widgets\Menu;
use humhub\modules\user\widgets\ProfileMenu;

return [
    'id' => 'wiki',
    'class' => 'humhub\modules\wiki\Module',
    'namespace' => 'humhub\modules\wiki',
    'urlManagerRules' => [
        ['class' => 'humhub\modules\wiki\components\WikiPageUrlRule'],
    ],
    'events' => [
        ['class' => Menu::class, 'event' => Menu::EVENT_INIT, 'callback' => ['humhub\modules\wiki\Events', 'onSpaceMenuInit']],
        ['class' => ProfileMenu::class, 'event' => ProfileMenu::EVENT_INIT, 'callback' => ['humhub\modules\wiki\Events', 'onProfileMenuInit']],
        ['class' => 'humhub\modules\rest\Module', 'event' => 'restApiAddRules', 'callback' => ['humhub\modules\wiki\Events', 'onRestApiAddRules']],
        ['class' => 'humhub\modules\rest\definitions\UserDefinitions', 'event' => 'initAllUserData', 'callback' => ['humhub\modules\wiki\Events', 'onUserDefinitionsInitAllUserData']],
        ['class' => WallEntryControls::class, 'event' => WallEntryControls::EVENT_INIT, 'callback' => ['humhub\modules\wiki\Events', 'onWallEntryControlsInit']],
    ],
];
