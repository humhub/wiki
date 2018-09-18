<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\assets;

use humhub\modules\wiki\helpers\Url;
use Yii;
use humhub\modules\ui\view\components\View;
use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $publishOptions = ['forceCopy' => false];

    public $sourcePath = '@wiki/resources';

    public $css = [
        'css/wiki.css'
    ];

    public $js = [
        'js/humhub.wiki.js',
        'js/humhub.wiki.linkExtension.js'
    ];

    /**
     * @param View $view
     * @return AssetBundle
     */
    public static function register($view)
    {
        $view->registerJsConfig([
            'wiki' => [
                'text' => [
                    'pageindex' => Yii::t('WikiModule.base', 'Page index')
                ]
            ],
            'wiki.linkExtension' => [
                'text' => [
                    'pageNotFound' => Yii::t('WikiModule.base', 'Page not found')
                ],
                'extractTitleUrl' => Url::toExtractTitles()
            ]
        ]);
        return parent::register($view);
    }
}
