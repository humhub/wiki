<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $publishOptions = ['forceCopy' => true];

    public $sourcePath = '@wiki/resources';

    public $css = [
        'css/wiki.css'
    ];

    public $js = [
        'js/humhub.wiki.js',
        'js/humhub.wiki.linkExtension.js'
    ];
}
