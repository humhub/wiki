<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 18.09.2018
 * Time: 06:51
 */

namespace humhub\modules\wiki\helpers;

use cebe\markdown\block\HeadlineTrait;
use cebe\markdown\Parser;
use Yii;

class HeadlineExtractor extends Parser
{
    use HeadlineTrait;

    public $headLines = [];

    public static function extract($text)
    {
        $instance = new static();
        $instance->parse($text);
        return $instance->headLines;
    }

    /**
     * Renders a headline
     */
    protected function renderHeadline($block)
    {
        try {
            if(count($block['content']) === 1) {
                $this->headLines[] = $block['content'][0][1];
            }
        } catch (\Exception $e) {
            Yii::error($e);
        }
        return $this->renderAbsy($block['content']) ."\n";
    }
}