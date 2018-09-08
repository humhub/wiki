<?php

namespace humhub\modules\wiki;

use humhub\modules\wiki\helpers\Url;
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

            if (substr($url, 0, 10) !== 'file-guid-' && substr($url, 0, 1) !== '.' && substr($url, 0, 1) !== '/' && 
            		substr($url, 0, 7) !== 'http://' && substr($url, 0, 8) !== 'https://' &&
            		substr($url, 0, 7) !== 'mailto:') {
                  return Url::toWikiByTitle($url, Yii::$app->controller->contentContainer);
            }
        }



        return parent::handleInternalUrls($url);
    }

}
