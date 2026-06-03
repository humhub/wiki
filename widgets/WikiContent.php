<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 11.09.2018
 * Time: 13:20
 */

namespace humhub\modules\wiki\widgets;

use humhub\helpers\Html;
use humhub\widgets\JsWidget;

class WikiContent extends JsWidget
{
    public $jsWidget = 'wiki.Content';
    public $init = true;

    public $cssClass;
    public $title;
    public $titleIcon;

    public function init()
    {
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $body = ob_get_clean();

        return Html::tag('div', $this->renderTitle() . $body, $this->getOptions());
    }

    public function getAttributes()
    {
        return [
            'class' => 'wiki-content' . ($this->cssClass ? ' ' . $this->cssClass : ''),
        ];
    }

    protected function renderTitle()
    {
        $icon = $this->titleIcon ? Html::tag('i', '', ['class' => 'fa ' . $this->titleIcon]) : '';

        return empty($this->title) ? '' : Html::tag('h1', (empty($icon)) ? $this->title : $icon . ' ' . $this->title);
    }
}
