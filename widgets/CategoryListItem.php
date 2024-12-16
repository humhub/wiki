<?php

/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 07.09.2018
 * Time: 15:08
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\HierarchyItem;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\services\HierarchyListService;

class CategoryListItem extends Widget
{
    public HierarchyListService|null $service = null;
    public HierarchyItem|null $item = null;

    /**
     * @var string
     */
    public $title;

    public ?string $icon = null;
    public ?string $iconPage = null;
    public ?string $iconCategory = null;

    /**
     * @var HierarchyItem[]
     */
    public $subItems;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var bool
     */
    public $hideTitle = false;

    /**
     * @var bool
     */
    public $showAddPage;

    /**
     * @var bool
     */
    public $showDrag;

    /**
     * @var bool
     */
    public $showNumFoldedSubpages;

    /**
     * @var bool|null
     */
    private static $canAdminister = null;

    /**
     * @var bool|null
     */
    public static $canCreate = null;

    /**
     * @var int Level of the sub-category
     */
    public $level = 0;

    /**
     * @var int Text indent for level of the sub-category
     */
    public $levelIndent = 40;

    /**
     * @var int|null Max level deep to load sub-pages, null - to load all levels
     */
    public $maxLevel;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->maxLevel !== null && $this->level > $this->maxLevel) {
            return '';
        }

        if ($this->showDrag === null) {
            $this->showDrag = $this->canAdminister();
        }

        if ($this->showAddPage === null) {
            $this->showAddPage = $this->canCreate();
        }

        $displaySubPages = $this->maxLevel === null || $this->level < $this->maxLevel;

        if ($this->item) {
            $this->title = $this->item->title;
            $this->subItems = $displaySubPages ? $this->service->getItemsByParentId($this->item->id) : [];
        }

        return $this->render('categoryListItem', [
            'service' => $this->service,
            'icon' => $this->icon,
            'iconPage' => $this->iconPage,
            'iconCategory' => $this->iconCategory,
            'title' => $this->title,
            'subItems' => $this->subItems,
            'hideTitle' => $this->hideTitle,
            'showAddPage' => $this->showAddPage,
            'showDrag' => $this->showDrag,
            'showNumFoldedSubpages' => $this->showNumFoldedSubpages,
            'contentContainer' => $this->contentContainer,
            'item' => $this->item,
            'level' => $this->level,
            'levelIndent' => $this->levelIndent,
            'maxLevel' => $this->maxLevel,
            'displaySubPages' => $displaySubPages,
        ]);
    }

    public static function clear()
    {
        static::$canAdminister = null;
        static::$canCreate = null;
    }

    private function canAdminister()
    {
        if (static::$canAdminister === null) {
            static::$canAdminister =  $this->contentContainer->can(AdministerPages::class);
        }

        return static::$canAdminister;
    }

    private function canCreate()
    {
        if (static::$canCreate === null) {
            static::$canCreate = (new WikiPage($this->contentContainer))->content->canEdit();
        }

        return static::$canCreate;
    }
}
