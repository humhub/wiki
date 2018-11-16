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
        // WikiLinks have to be parsed before internal links!
        $output = $this->parseWikiLinks($output);
        $output = $this->parseInternalLinks($output);
        return $output;

    }

    public function parseInternalLinks($output) {
        return preg_replace_callback(static::getLinkPattern(), function($match) {
            $url = $match[2];

            if(empty($url)) {
                return $match[0];
            }

            if(strpos($url, "file-guid-") !== 0 && strpos($url, "file-guid:") !== 0 && $url[0] !== '.' && $url[0] !== '/' && strpos($url, ':') === false) {
                $page = WikiPage::findOne(['title' => $match[2]]);
                return $this->toWikiLink($match[1], $page);
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
            return $this->toWikiLink($match[1],  WikiPage::findOne(['id' => $match[3]]), null, $match[4]);
        });
    }

    public function toWikiLink($label, $page, $title = null, $anchor = null)
    {
        if(!$page) {
            // page not found format is [<label>](wiki:#)
            return  $this->toWikiLink($label, '#');
        }

        if($page instanceof WikiPage) {
            // In edit mode we use wiki:<wikiId> format in rendered richtext we use actual wiki url
            $url = $this->edit ? $page->id : Url::toWiki($page);

            if($anchor) {
                $url .= '#'.$anchor;
            }

            return $this->toWikiLink($label, $url, $page->title);
        }

        return '['.$label.'](wiki:'.$page.' "'.$title.'")';
    }

}