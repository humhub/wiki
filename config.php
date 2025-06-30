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
        ['class' => 'humhub\modules\legal\services\ExportService', 'event' => 'collectUserData', 'callback' => ['humhub\modules\wiki\Events', 'onLegalModuleUserDataExport']],
        ['class' => WallEntryControls::class, 'event' => WallEntryControls::EVENT_INIT, 'callback' => ['humhub\modules\wiki\Events', 'onWallEntryControlsInit']],
        ['class' => 'humhub\modules\custom_pages\modules\template\services\ElementTypeService', 'event' => 'init', 'callback' => ['humhub\modules\wiki\Events', 'onCustomPagesTemplateElementTypeServiceInit']],
    ],
];
