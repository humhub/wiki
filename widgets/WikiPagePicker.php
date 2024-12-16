<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\ui\form\widgets\BasePicker;
use humhub\modules\wiki\models\WikiPage;
use Yii;

/**
 * WikiPagePicker input field for selecting wiki page as parent page/category.
 */
class WikiPagePicker extends BasePicker
{
    /**
     * @inheritdoc
     */
    public $minInput = 2;

    /**
     * @inheritdoc
     */
    public $maxSelection = 1;

    /**
     * @inheritdoc
     */
    public $defaultRoute = '/wiki/page/picker-search';

    /**
     * @inheritdoc
     */
    public $itemClass = WikiPage::class;

    /**
     * @inheritdoc
     */
    public $model;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->model instanceof WikiPage) {
            $params = $this->model->isNewRecord ? [] : ['id' => $this->model->id];
            $this->url = $this->model->content->container->createUrl($this->defaultRoute, $params);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        $result = parent::getData();

        if ($this->placeholderMore === null) {
            $result['placeholder-more'] = Yii::t('WikiModule.base', 'Link to page...');
        }

        if ($this->maxSelection) {
            $result['maximum-selected'] = Yii::t('WikiModule.base', 'This field only allows a maximum 1 page.');
        }

        return $result;
    }

    /**
     * @inheritdoc
     * @param WikiPage $item selected item
     */
    protected function getItemText($item)
    {
        return $item->title;
    }

    /**
     * @inheritdoc
     */
    protected function getItemImage($item)
    {
        return null;
    }

}
