<?php

use humhub\modules\user\models\User;
use humhub\modules\wiki\models\WikiPage;
use yii\helpers\Html;

/* @var $originator User */
/* @var $source WikiPage */

echo Yii::t('WikiModule.base', '{userName} edited the Wiki page "{wikiPageTitle}".', [
    '{userName}' => Html::tag('strong', Html::encode($originator->displayName)),
    '{wikiPageTitle}' => Html::encode($this->context->getContentInfo($source, false)),
]);