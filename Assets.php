<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace module\wiki;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $sourcePath = '@module/wiki/assets';
    public $css = [
        'wiki.css'
    ];

}
