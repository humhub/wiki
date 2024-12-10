<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use humhub\modules\content\models\Content;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\models\WikiPage;
use Yii;

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

    public $numberingString;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $icon = $this->iconCategoryOpened;

        if ($this->page) {
            $this->title = $this->page->title;
            if ($this->page->isCategory) {
                $displaySubPages = $this->maxLevel === null || $this->level < $this->maxLevel;
                $icon = !$displaySubPages || $this->page->isFolded() ? $this->iconCategoryFolded : $this->iconCategoryOpened;
            } else {
                $icon = $this->iconPage;
            }
            $this->numberingString = $this->page->getNumbering();
        }

        if ($this->titleInfo === null &&
            $this->showNumFoldedSubpages &&
            ($this->maxLevel !== null && $this->level === $this->maxLevel) &&
            $this->page->childrenCount) {
            $this->titleInfo = Yii::t('WikiModule.base', '({n,plural,=1{+1 subpage}other{+{count} subpages}})', ['n' => $this->page->childrenCount, 'count' => $this->page->childrenCount]);
        }

        $module = Yii::$app->getModule('wiki');        
        $numberingEnabled = $module->settings->space()->get('overviewNumberingEnabled');
        
        return $this->render('pageListItemTitle', [
            'page' => $this->page,
            'title' => $numberingEnabled ? ($this->numberingString . ' ' . $this->title) : $this->title,
            'titleIcon' => $this->getVisibilityIcon(),
            'titleInfo' => $this->titleInfo,
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
            'class' => 'page-title' . ($this->page && $this->page->isCategory ? ' page-is-category' : ''),
            'style' => 'padding-left:' . (12 + $this->level * $this->levelIndent) . 'px',
        ];

        if (Helper::isCurrentPage($this->page)) {
            $options['class'] .= ' page-current';
        }

        return $options;
    }

    public function getVisibilityIcon(): ?Icon
    {
        if (!$this->page instanceof WikiPage) {
            return null;
        }

        $content = $this->page->content;
        if ($content->visibility === $content->container->getDefaultContentVisibility()) {
            return null;
        }

        return Icon::get($content->visibility === Content::VISIBILITY_PUBLIC ? 'globe' : 'lock')
            ->class('page-title-icon-visibility');
    }

}
