<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
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
    public $iconPage = 'fa-file-text-o';
    public $iconCategoryOpened = 'fa-caret-square-o-down';
    public $iconCategoryFolded = 'fa-caret-square-o-right';

    /**
     * @var bool
     */
    public $showAddPage = false;

    /**
     * @var bool
     */
    public $showDrag = false;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $icon = $this->iconCategoryOpened;

        if ($this->page) {
            $this->title = $this->page->title;
            if ($this->page->is_category) {
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
        ]);
    }

}