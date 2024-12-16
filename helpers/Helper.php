<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\helpers;

use humhub\modules\ui\view\helpers\ThemeHelper;
use Yii;

/**
 * Class Helper
 */
class Helper
{
    public static function isEnterpriseTheme(): bool
    {
        return array_key_exists('enterprise', ThemeHelper::getThemeTree(Yii::$app->view->theme));
    }
}
