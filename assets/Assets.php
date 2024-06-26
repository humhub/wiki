<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\assets;

use humhub\assets\JqueryWidgetAsset;
use humhub\components\assets\AssetBundle;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\Module;
use Yii;

class Assets extends AssetBundle
{
    /**
     * v1.5 compatibility defer script loading
     *
     * Migrate to HumHub AssetBundle once minVersion is >=1.5
     *
     * @var bool
     */
    public $defer = true;

    public $sourcePath = '@wiki/resources';

    public $forceCopy = false;

    public $css = [
        'css/humhub.wiki.min.css',
    ];

    public $js = [
        'js/humhub.wiki.bundle.min.js',
        //'js/jquery.ui.touch-punch.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        JqueryWidgetAsset::class,
    ];

    /**
     * @param View $view
     * @return AssetBundle
     */
    public static function register($view)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('wiki');

        $view->registerJsConfig([
            'wiki' => [
                'text' => [
                    'pageindex' => Yii::t('WikiModule.base', 'Table of Contents'),
                ],
            ],
            'wiki.Page' => [
                'tocMaxH3' => $module->tocMaxH3,
            ],
            'wiki.linkExtension' => [
                'text' => [
                    'pageNotFound' => Yii::t('WikiModule.base', 'Page not found'),
                ],
                'extractTitleUrl' => Url::toExtractTitles(),
            ],
            'humhub.wiki.CategoryListView' => [
                'updateFoldingStateUrl' => Url::toUpdateFoldingState(),
            ],
        ]);
        return parent::register($view);
    }
}
