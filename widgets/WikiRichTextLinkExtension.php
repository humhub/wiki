<?php


namespace humhub\modules\wiki\widgets;


use humhub\modules\content\widgets\richtext\extensions\link\LinkParserBlock;
use humhub\modules\content\widgets\richtext\extensions\link\RichTextLinkExtension;
use humhub\modules\content\widgets\richtext\extensions\link\RichTextLinkExtensionMatch;
use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use humhub\modules\file\models\File;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;

/**
 * Wiki link extension uses the following format:
 *
 * [<text>](wiki:label "url")
 * $match[0]: markdown, $match[1]: name, $match[2]: extension(wiki) $match[3]: wikiId
 */
class WikiRichTextLinkExtension extends RichTextLinkExtension
{
    public $key = 'wiki';

    /**
     * @inheritdoc
     */
    public function onBeforeOutput(ProsemirrorRichText $richtext, string $output) : string
    {
        $output = $this->parseWikiLinks($richtext->edit, $output);
        return $this->parseInternalLinks($richtext->edit, $output);
    }

    /**
     * @inheritdoc
     */
    public function parseWikiLinks(bool $isEdit, string $output)
    {
        return static::replace($output, function(RichTextLinkExtensionMatch $match) use($isEdit) {
            return $this->toWikiLink($isEdit, $match->getText(),  WikiPage::findOne(['id' => $match->getExtensionId()]), null, $match->getTitle());
        });
    }

    /**
     * Parses for legacy wiki links
     * @param bool $isEdit
     * @param string $output
     * @return string
     */
    private function parseInternalLinks(bool $isEdit, string $output)
    {
        return preg_replace_callback(static::getLinkPattern(), function($match) use($isEdit) {
            $url = $match[2];

            if(empty($url)) {
                return $match[0];
            }

            if(strpos($url, "file-guid-") !== 0 && strpos($url, "file-guid:") !== 0 && $url[0] !== '.' && $url[0] !== '/' && strpos($url, ':') === false) {
                $page = WikiPage::findOne(['title' => $match[2]]);
                return $this->toWikiLink($isEdit, $match[1], $page);
            }

            if(!$isEdit) {
                if (strpos($url, "file-guid-") === 0) {
                    $guid = str_replace('file-guid-', '', $url);
                    $file = File::findOne(['guid' => $guid]);
                    if ($file !== null) {
                        return '['.$match[1].']('.$file->getUrl([], true).')';
                    }
                }
            }

            return $match[0];
        }, $output);
    }

    /**
     * @inheritdoc
     */
    private static function getLinkPattern()
    {
        return '/(?<!\\\\)\[([^\]]*)\]\(([^\)\s]*)(?:\s")?([^\)"]*)?(?:")?\)/is';
    }

    private function toWikiLink($isEdit, $label, $page, $title = null, $anchor = null)
    {
        if(!$page) {
            // page not found format is [<label>](wiki:#)
            return  $this->toWikiLink($isEdit, $label, '#');
        }

        if($page instanceof WikiPage) {
            // In edit mode we use wiki:<wikiId> format in rendered richtext we use actual wiki url
            $url = $isEdit ? $page->id : Url::toWiki($page);

            if($anchor) {
                $url .= '#'. urlencode($anchor);
            }

            return $this->toWikiLink($isEdit, $label, $url, $page->title);
        }

        return RichTextLinkExtension::buildLink($label,'wiki:'. $page, $title );
    }

    /**
     * @inheritdoc
     */
    public function onBeforeConvertLink(LinkParserBlock $linkBlock) : void
    {
        $wikiId = $this->cutExtensionKeyFromUrl($linkBlock->getUrl());

        $page = WikiPage::findOne(['id' => $wikiId]);

        if(!$page) {
            $linkBlock->setResult('['.$linkBlock->getParsedText().']');
            return;
        }

        $linkBlock->setUrl($page->getUrl());
    }
}