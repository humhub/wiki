<?php


namespace humhub\modules\wiki\widgets;

use humhub\modules\file\models\File;
use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use Yii;

class WikiRichText extends ProsemirrorRichText
{
    public $preset = self::PRESET_DOCUMENT;

    public function isCompatibilityMode()
    {
        return false;
    }

    protected function parseOutput()
    {
        $output = parent::parseOutput();

        return preg_replace_callback(static::getLinkPattern(), function($match) {
            $url = $match[2];

            if(strpos($url, "file-guid-") !== 0 && strpos($url, "file-guid:") !== 0 && $url[0] !== '.' && $url[0] !== '/' && strpos($url, ':') === false) {
                return '['.$match[1].']('.Yii::$app->controller->contentContainer->createUrl('/wiki/page/view', ['title' => $match[2]]).')';
            }

            if(!$this->edit) {
                if (strpos($url, "file-guid-") === 0) {

                    $guid = str_replace('file-guid-', '', $url);
                    $file = File::findOne(['guid' => $guid]);
                    if ($file !== null) {
                        return '['.$match[1].']('.$file->getUrl([], false).')';
                    }
                }
            }


            return $match[0];
        }, $output);
    }

    protected static function getLinkPattern()
    {
        return '/(?<!\\\\)\[([^\]]*)\]\(([^\)\s]*)(?:\s")?([^\)"]*)?(?:")?\)/is';
    }
}