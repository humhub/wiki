<?php


namespace humhub\modules\wiki\widgets;


use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\content\widgets\richtext\RichText;

class WikiEditor extends ProsemirrorRichTextEditor
{
    public $preset = 'wiki';

    public static  $renderer = [
        'class' => WikiRichText::class
    ];

}