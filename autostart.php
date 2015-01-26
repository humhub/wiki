<?php

Yii::app()->moduleManager->register(array(
    'id' => 'wiki',
    'class' => 'application.modules.wiki.WikiModule',
    'import' => array(
        'application.modules.wiki.*',
        'application.modules.wiki.models.*',
    ),
    'events' => array(
        array('class' => 'SpaceMenuWidget', 'event' => 'onInit', 'callback' => array('WikiModuleEvents', 'onSpaceMenuInit')),
        array('class' => 'ProfileMenuWidget', 'event' => 'onInit', 'callback' => array('WikiModuleEvents', 'onProfileMenuInit')),
    ),
));
?>