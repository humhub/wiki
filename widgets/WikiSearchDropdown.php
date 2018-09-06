<?php


namespace humhub\modules\wiki\widgets;


use humhub\modules\ui\form\widgets\JsInputWidget;
use yii\helpers\Url;

class WikiSearchDropdown extends JsInputWidget
{
    public $jsWidget = 'wiki.SearchDropdown';

    public $init = true;

    public $placeholder;

    public function run()
    {
        return $this->render('wikiSearchDropDown', [
            'model' => $this->model,
            'field' => $this->attribute,
            'container' => $this->container,
            'options' => $this->getOptions(),
        ]);
    }

    public function getAttributes()
    {
        return [
            'class' => 'form-control',
            'placeholder' => $this->placeholder
        ];
    }

    public function getData()
    {
        return [
          'search-url' => Url::to(['/wiki/search/search'])
        ];
    }
}