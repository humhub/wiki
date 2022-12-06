<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\models\WikiPage;

class PageListItemTitle extends Widget
{
    /**
     * @var WikiPage
     */
    public $page = null;

    /**
     * @var string
     */
    public $title;

    public $icon;
    public $iconPage = '';
    public $iconCategoryOpened = 'fa-caret-down';
    public $iconCategoryFolded = 'fa-caret-right';

    /**
     * @var bool
     */
    public $showAddPage = false;

    /**
     * @var bool
     */
    public $showDrag = false;

    /**
     * @var int Level of the sub-category
     */
    public $level = 0;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $icon = $this->iconCategoryOpened;

        if ($this->page) {
            $this->title = $this->page->title;
            if ($this->page->isCategory) {
                $icon = $this->page->isFolded() ? $this->iconCategoryFolded : $this->iconCategoryOpened;
            } else {
                $icon = $this->iconPage;
            }
        }

        return $this->render('pageListItemTitle', [
            'page' => $this->page,
            'title' => $this->title,
            'url' => $this->page ? $this->page->getUrl() : null,
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
            'class' => 'page-title',
            'style' => 'padding-left:' . (12 + $this->level * 20) .'px',
        ];

        if (Helper::isCurrentPage($this->page)) {
            $options['class'] .= ' page-current';
        }

        return $options;
    }

}