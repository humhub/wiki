<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 07.09.2018
 * Time: 14:50
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\services\HierarchyListService;
use humhub\widgets\JsWidget;

class CategoryListView extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'wiki.CategoryListView';

    /**
     * @inheritdoc
     */
    public $id = 'category_list_view';

    /**
     * @inheritdoc
     */
    public $init = true;

    public HierarchyListService|null $service = null;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    public ?int $parentId = null;

    /**
     * @var bool
     */
    public $showAddPage;

    /**
     * @var bool
     */
    public $showDrag;

    /**
     * @var int Level of the sub-category
     */
    public $level = 0;

    /**
     * @var int Text indent for level of the sub-category
     */
    public $levelIndent = 17;

    /**
     * @var int|null Max level deep to load sub-pages, null - to load all levels
     */
    public $maxLevel;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->service === null) {
            $this->service = new HierarchyListService($this->contentContainer);
        }
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function run()
    {
        $items = $this->service->getItemsByParentId($this->parentId);

        if ($items === []) {
            return '';
        }

        return $this->render('categoryListView', [
            'options' => $this->getOptions(),
            'service' => $this->service,
            'items' => $items,
            'contentContainer' => $this->contentContainer,
            'showAddPage' => $this->showAddPage,
            'showDrag' => $this->showDrag,
            'level' => $this->level,
            'levelIndent' => $this->levelIndent,
            'maxLevel' => $this->maxLevel,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        $attrs = ['class' => 'wiki-page-list'];

        if ($this->service->isFoldedItemById($this->parentId)) {
            $attrs['style'] = 'display:none';
        }

        return $attrs;
    }

    public function getData()
    {
        return [
            'drop-url' => $this->contentContainer->createUrl('/wiki/page/sort'),
            'icon-page' => '',
            'icon-category' => 'fa fa-caret-down',
        ];
    }
}
