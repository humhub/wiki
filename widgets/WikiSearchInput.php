<?php


namespace humhub\modules\wiki\widgets;


use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\form\widgets\JsInputWidget;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\permissions\CreatePage;
use yii\helpers\Url;

class WikiSearchInput extends JsInputWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'wiki.linkExtension.SearchInput';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var string
     */
    public $placeholder;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    private $items = [];

    private $itemOptions = [];

    /**
     * @inheritdoc
     */
    public function run()
    {

        return $this->render('wikiSearchInput', [
            'model' => $this->model,
            'field' => $this->attribute,
            'container' => $this->container,
            'items' => $this->loadItems(),
            'options' => $this->getOptions(),
        ]);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function loadItems()
    {
        $pages = WikiPage::find()->contentContainer($this->contentContainer)->readable()->all();
        foreach ($pages as $page) {
            $this->items[$page->id] = Html::encode($page->title);
        }

        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return [
            'class' => 'form-control',
            'placeholder' => $this->placeholder,
            'style' => 'width:100%'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $data = [
            'search-url' => Url::to(['/wiki/search/search']),
            'ui-select2' => ''
        ];

        if ($this->contentContainer->can(CreatePage::class)) {
            $data['ui-select2-allow-new'] = '';
            $data['ui-select2-new-sign'] = '➕';
        }

        return $data;
    }
}