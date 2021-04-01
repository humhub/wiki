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

    public static function getExtensions()
    {
        $result = parent::getExtensions();
        $result[] = new WikiRichTextLinkExtension();
        return $result;
    }
}