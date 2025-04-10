<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\models\HierarchyItem;
use humhub\modules\wiki\services\HierarchyListService;
use Yii;

class PageListItemTitle extends Widget
{
    public HierarchyListService|null $service = null;
    public HierarchyItem|null $item = null;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $titleInfo;

    public $icon;
    public $iconPage;
    public $iconCategoryOpened = 'caret-down';
    public $iconCategoryFolded = 'caret-right';

    /**
     * @var bool
     */
    public $showAddPage = false;

    /**
     * @var bool
     */
    public $showDrag = false;

    /**
     * @var bool
     */
    public $showNumFoldedSubpages = false;

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
        $icon = $this->iconCategoryOpened;

        if ($this->item) {
            $this->title = $this->item->title;
            if ($this->item->isCategory) {
                $displaySubPages = $this->maxLevel === null || $this->level < $this->maxLevel;
                $icon = !$displaySubPages || $this->item->isFolded ? $this->iconCategoryFolded : $this->iconCategoryOpened;
            } else {
                $icon = $this->iconPage;
            }
        }

        if ($this->titleInfo === null &&
            $this->showNumFoldedSubpages &&
            ($this->maxLevel !== null && $this->level === $this->maxLevel)) {
            if ($childrenCount = $this->service->getItemChildrenCount($this->item->id)) {
                $this->titleInfo = Yii::t('WikiModule.base', '({n,plural,=1{+1 subpage}other{+{count} subpages}})', ['n' => $childrenCount, 'count' => $childrenCount]);
            }
        }

        return $this->render('pageListItemTitle', [
            'service' => $this->service,
            'item' => $this->item,
            'title' => $this->title,
            'titleIcon' => $this->getVisibilityIcon(),
            'titleInfo' => $this->titleInfo,
            'url' => $this->service->getWikiUrl($this->item),
            'icon' => $this->icon ?? $icon,
            'showDrag' => $this->showDrag,
            'showAddPage' => $this->showAddPage,
            'options' => $this->getOptions(),
            'level' => $this->level,
        ]);
    }

    public function getOptions(): array
    {
        $options = [
            'class' => 'page-title' . ($this->item && $this->item->isCategory ? ' page-is-category' : ''),
            'style' => 'padding-left:' . (12 + $this->level * $this->levelIndent) . 'px',
        ];

        if ($this->service->isCurrentItem($this->item)) {
            $options['class'] .= ' page-current';
        }

        return $options;
    }

    public function getVisibilityIcon(): ?Icon
    {
        $icon = $this->service->getItemVisibilityIconName($this->item);

        return $icon ? Icon::get($icon)->class('page-title-icon-visibility') : null;
    }

}
