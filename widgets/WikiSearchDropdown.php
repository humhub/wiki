<?php


namespace humhub\modules\wiki\widgets;


use humhub\modules\ui\form\widgets\JsInputWidget;

class WikiSearchDropdown extends JsInputWidget
{
    public $jsWidget = 'wiki.search';

    public function run()
    {
        return $this->render('wikiSearchDropDown', [
            'model' => $this->model,
            'field' => $this->attribute,
            'container' => $this->container,
            'inputId' => $this->getId(true)
        ]);
    }
}