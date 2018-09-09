<?php


namespace humhub\modules\wiki\widgets;

use humhub\modules\file\models\File;
use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use Yii;

class WikiRichText extends ProsemirrorRichText
{
    public $preset = 'wiki';

    public function isCompatibilityMode()
    {
        return false;
    }

    protected function parseOutput()
    {
        $output = parent::parseOutput();
        $output = $this->parseInternalLinks($output);
        $output = $this->parseWikiLinks($output);
        return $output;

    }

    public function parseInternalLinks($output) {
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

    public function parseWikiLinks($text)
    {
        // $match[0]: markdown, $match[1]: name, $match[2]: extension(wiki) $match[3]: wikiId
        return static::replaceLinkExtension($text, 'wiki', function($match) {
            $page = WikiPage::findOne(['id' => $match[3]]);

            if(!$page) {
                return  '['.$match[1].'](wiki:'.$match[3].' "#")';
            } else if(!$this->edit) {
                return  '['.$match[1].'](wiki:'.Url::toWiki($page).' "'.$page->title.'")';
            } else {
                return  '['.$match[1].'](wiki:'.$page->id.' "'.$page->title.'")';
            }
        });
    }
}