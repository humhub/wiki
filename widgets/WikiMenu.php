<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 11.09.2018
 * Time: 13:20
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\MoveContentLink;
use humhub\modules\ui\menu\DropdownDivider;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\DropdownMenu;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\ViewHistory;
use humhub\widgets\Link;
use Yii;

class WikiMenu extends DropdownMenu
{
    const LINK_HOME = 'home';
    const LINK_INDEX = 'index';

    const LINK_EDIT = 'edit';
    const LINK_HISTORY = 'history';
    const LINK_PERMA = 'perma';

    const LINK_EDIT_CANCEL = 'edit_cancel';
    const LINK_EDIT_SAVE = 'edit_save';
    const LINK_EDIT_DELETE = 'edit_delete';

    const LINK_BACK_TO_PAGE = 'back_to_page';

    const LINK_REVERT = 'revert';
    const LINK_REVERT_GO_BACK = 'revert_go_back';

    const LINK_MOVE = 'move';

    const LINK_NEW = 'new';

    const BLOCK_START = [self::LINK_HOME, self::LINK_INDEX];

    const BLOCK_PAGE_VIEW =  [self::LINK_EDIT, self::LINK_HISTORY, self::LINK_PERMA];

    const BLOCK_REVISION_VIEW =  [self::LINK_REVERT, self::LINK_REVERT_GO_BACK];

    const BLOCK_EDIT =  [self::LINK_EDIT_SAVE, self::LINK_EDIT_CANCEL, self::LINK_EDIT_DELETE, self::LINK_MOVE];

    const BLOCK_BOTTOM =  [self::LINK_NEW];

    /**
     * @var WikiPage
     */
    public $page;

    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    /**
     * @var WikiPageRevision
     */
    public $revision;

    /**
     * @var WikiPage
     */
    public $home;

    /**
     * @var array
     */
    public $excludes = [];

    /**
     * @var bool
     */
    private $canEdit = false;

    /**
     * @var bool
     */
    private $canDelete = false;

    /**
     * @var bool edit page view
     */
    public $edit = false;

    /**
     * @var array
     */
    public $blocks = [];

    /**
     * @var array|string
     */
    public $buttons = [];

    /**
     * @inheritdoc
     */
    public $template = 'menu';

    public function init()
    {
        if (!$this->container && $this->page) {
            $this->container = $this->page->content->container;
        }

        if (!$this->home) {
            $this->home = WikiPage::getHome($this->container);
        }

        if ($this->page) {
            $this->canEdit = $this->page->canEditWikiPage();
            $this->canDelete = !$this->page->isNewRecord && $this->canAdminister();
        }

        $this->initEntries();

        parent::init();
    }

    protected function setDefaults()
    {
        if ($this->edit) {
            $this->blocks = [static::BLOCK_EDIT, static::BLOCK_START];
        } else if ($this->page && (!$this->revision || $this->revision->is_latest)) {
            $this->blocks = [static::BLOCK_START, static::BLOCK_PAGE_VIEW, static::BLOCK_BOTTOM];
        } else if ($this->page && $this->revision) {
            $this->blocks = [static::BLOCK_START, static::BLOCK_REVISION_VIEW, static::BLOCK_BOTTOM];
        } else {
            $this->blocks = [static::BLOCK_START, static::BLOCK_BOTTOM];
        }
    }

    protected function initEntries()
    {
        if (empty($this->blocks)) {
            $this->setDefaults();
        }

        if (is_string($this->buttons)) {
            $this->buttons = [$this->buttons];
        }

        $sortOrder = 100;
        $blockEntriesCount = [];
        foreach ($this->blocks as $blockIndex => $block) {
            $dividerWasPrinted = false;
            $blockEntriesCount[$blockIndex] = 0;
            foreach ($block as $link) {
                if (in_array($link, $this->buttons)) {
                    continue;
                }

                $link = $this->getLink($link);
                if ($link instanceof MenuLink) {
                    if (!$dividerWasPrinted && !empty($blockEntriesCount[$blockIndex - 1])) {
                        $this->addEntry(new DropdownDivider(['sortOrder' => $sortOrder]));
                        $sortOrder += 100;
                        $dividerWasPrinted = true;
                    }

                    $link->setSortOrder($sortOrder);
                    $this->addEntry($link);
                    $sortOrder += 100;
                    $blockEntriesCount[$blockIndex]++;
                }
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->trigger(static::EVENT_RUN);

        if (empty($this->template) || (empty($this->buttons) && empty($this->entries))) {
            return '';
        }

        return $this->render($this->template, $this->getViewParams());
    }

    /**
     * Render a button
     *
     * @param string $button
     * @return Link|string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderButton(string $button)
    {
        $button = $this->getLink($button);

        if ($button instanceof MenuLink) {
            $htmlOptions = $button->getHtmlOptions();
            if (isset($htmlOptions['btn-type'])) {
                $btnTypeClass = 'btn-' . $htmlOptions['btn-type'];
                unset($htmlOptions['btn-type']);
            } else {
                $btnTypeClass = 'btn-info';
            }
            return Link::to($button->icon . ' ' . $button->getLabel(), $button->getUrl())
                ->cssClass('btn btn-sm ' . $btnTypeClass)
                ->options($htmlOptions);
        }

        return '';
    }

    /**
     * @param string $link
     * @return MenuLink|null
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    private function getLink($link)
    {
        switch ($link) {
            case static::LINK_HOME:
                return $this->home ? new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Home'),
                    'url' => Url::toWiki($this->home),
                    'icon' => 'fa-home',
                ]) : null;
            case static::LINK_INDEX:
                return new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Index'),
                    'url' => Url::toOverview($this->container),
                    'icon' => 'fa-list-alt',
                ]);
            case static::LINK_EDIT:
                return $this->canEdit ? new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Edit'),
                    'url' => Url::toWikiEdit($this->page),
                    'icon' => 'fa-pencil',
                ]) : null;
            case static::LINK_HISTORY:
                return $this->canViewHistory() ? new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Page History'),
                    'url' => Url::toWikiHistory($this->page),
                    'icon' => 'fa-clock-o history',
                ]) : null;
            case static::LINK_PERMA:
                return new MenuLink([
                    'label' => Yii::t('ContentModule.base', 'Permalink'),
                    'url' => '#',
                    'icon' => 'fa-link',
                    'htmlOptions' => [
                        'data-action-click' => 'content.permalink',
                        'data-content-permalink' => \yii\helpers\Url::to(['/content/perma', 'id' => $this->page->content->id], true),
                    ],
                ]);
            case static::LINK_REVERT:
                return $this->canEdit ? new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Revert this'),
                    'url' => '#',
                    'icon' => 'fa-history',
                    'htmlOptions' => [
                        'btn-type' => 'warning',
                        'data-action-click' => 'wiki.revertRevision',
                        'data-action-click-url' => Url::toWikiRevertRevision($this->page, $this->revision),
                        'data-action-confirm-header' => Yii::t('WikiModule.base', '<strong>Confirm</strong> page reverting'),
                        'data-action-confirm' => Yii::t('WikiModule.base', 'Do you really want to revert this page?'),
                        'data-action-confirm-text' => Yii::t('WikiModule.base', 'Revert'),
                    ],
                ]) : null;
            case static::LINK_REVERT_GO_BACK:
                return new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Go back'),
                    'url' => Url::toWikiHistory($this->page),
                    'icon' => 'fa-reply',
                ]);
            case static::LINK_NEW:
                return $this->canCreatePage() ? new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'New page'),
                    'url' => Url::toWikiCreate($this->container, $this->page ? $this->page->id : null),
                    'icon' => 'fa-plus new',
                ]) : null;
            case static::LINK_EDIT_DELETE:
                return $this->canDelete ? new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Delete'),
                    'url' => '#',
                    'icon' => 'fa-trash-o delete',
                    'htmlOptions' => [
                        'data-action-click' => 'wiki.delete',
                        'data-action-click-url' => Url::toWikiDelete($this->page),
                        'data-action-confirm' => '',
                    ],
                ]) : null;
            case static::LINK_EDIT_CANCEL:
                return new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Cancel'),
                    'url' => $this->page->isNewRecord ? Url::toOverview($this->container) : Url::toWiki($this->page),
                    'icon' => 'fa-reply',
                ]);
            case static::LINK_EDIT_SAVE:
                return new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Save'),
                    'url' => '#',
                    'icon' => 'fa-save',
                    'htmlOptions' => [
                        'btn-type' => 'primary',
                        'data-action-click' => 'wiki.Form.submit',
                    ],
                ]);
            case static::LINK_BACK_TO_PAGE:
                return new MenuLink([
                    'label' => Yii::t('WikiModule.base', 'Back to page'),
                    'url' => Url::toWiki($this->page),
                    'icon' => 'fa-reply',
                ]);
            case static::LINK_MOVE:
                if (!$this->page->isNewRecord && $this->page->canMove()) {
                    $moveLink = new MoveContentLink(['model' => $this->page]);
                    return new MenuLink([
                        'label' => Yii::t('ContentModule.base', 'Move content'),
                        'url' => '#',
                        'icon' => $moveLink->icon,
                        'htmlOptions' => [
                            'data-action-click' => $moveLink->action,
                            'data-action-url' => $moveLink->getActionUrl(),
                        ],
                    ]);
                }
                return null;
        }

        return null;
    }

    /**
     * @return boolean can create new wiki site
     * @throws \yii\base\InvalidConfigException
     */
    public function canCreatePage(): bool
    {
        return $this->container->can(CreatePage::class);
    }

    /**
     * @return boolean can view wiki page history?
     * @throws \yii\base\InvalidConfigException
     */
    public function canViewHistory(): bool
    {
        return $this->container->can(ViewHistory::class);
    }

    /**
     * @return boolean can manage wiki sites?
     * @throws \yii\base\InvalidConfigException
     */
    public function canAdminister(): bool
    {
        return $this->container->can(AdministerPages::class);
    }

}