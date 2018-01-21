<?php

namespace humhub\modules\wiki;

use Yii;
use humhub\modules\content\components\ContentContainerController;

/**
 * WikiMarkdownParser also handles internal wiki urls
 *
 * @author luke
 */
class Markdown extends \humhub\libs\Markdown
{

    public $enableNewlines = true;

    
    protected function handleInternalUrls($url)
    {

        if (Yii::$app->controller instanceof ContentContainerController) {
            if (substr($url, 0, 7) == 'file://') {
                return $url;
            }

            if (substr($url, 0, 10) !== 'file-guid-' && substr($url, 0, 1) !== '.' && substr($url, 0, 1) !== '/' && substr($url, 0, 7) !== 'http://' && substr($url, 0, 8) !== 'https://') {
                return Yii::$app->controller->contentContainer->createUrl('/wiki/page/view', ['title' => $url]);
            }
        }



        return parent::handleInternalUrls($url);
    }
}
