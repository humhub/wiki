<?php

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\form\widgets\JsInputWidget;
use humhub\modules\wiki\models\WikiPage;
use yii\helpers\StringHelper;
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
        $pages = WikiPage::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->orderBy([
                'parent_page_id' => SORT_ASC,
                'sort_order' => SORT_ASC,
            ]);

        foreach ($pages->each() as $page) {
            /* @var WikiPage $page */
            $this->items[$page->id] = StringHelper::truncate(
                ($page->categoryPage ? $page->categoryPage->title . ' -> ' : '')
                . $page->title,
                250,
                ' [...]',
            );
        }

        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        $attributes = [
            'class' => 'form-control',
            'data-picker-placeholder' => $this->placeholder,
            'style' => 'width:100%',
        ];

        if ((new WikiPage($this->contentContainer))->content->canEdit()) {
            $attributes['data-picker-allow-new'] = true;
        }

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return [
            'search-url' => Url::to(['/wiki/search/search']),
        ];
    }
}
