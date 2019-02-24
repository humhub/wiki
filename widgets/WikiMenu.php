<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 11.09.2018
 * Time: 13:20
 */

namespace humhub\modules\wiki\widgets;


use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\MoveContentLink;
use humhub\modules\content\widgets\PermaLink;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\ViewHistory;
use humhub\widgets\Link;
use Yii;

class WikiMenu extends Widget
{
    const LINK_HOME = 'home';
    const LINK_INDEX = 'index';

    const LINK_EDIT = 'edit';
    const LINK_HISTORY = 'history';
    const LINK_PERMA = 'perma';

    const LINK_EDIT_CANCEL = 'edit_cancel';
    const LINK_EDIT_DELETE = 'edit_delete';

    const LINK_BACK_TO_PAGE = 'back_to_page';

    const LINK_REVERT = 'revert';
    const LINK_REVERT_GO_BACK = 'revert_go_back';

    const LINK_MOVE = 'move';

    const LINK_NEW = 'new';

    const BLOCK_START = [self::LINK_HOME, self::LINK_INDEX];

    const BLOCK_PAGE_VIEW =  [self::LINK_EDIT, self::LINK_HISTORY, self::LINK_PERMA];

    const BLOCK_REVISION_VIEW =  [self::LINK_REVERT, self::LINK_REVERT_GO_BACK];

    const BLOCK_EDIT =  [self::LINK_EDIT_CANCEL, self::LINK_EDIT_DELETE, self::LINK_MOVE];

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

    public $cols = 3;

    public function init()
    {
        if(!$this->container && $this->page) {
            $this->container = $this->page->content->container;
        }

        if(empty($this->blocks)) {
            $this->setDefaults();
        }

        if(!$this->home) {
            $this->home = WikiPage::getHome($this->container);
        }

        if($this->page) {
            $this->canEdit = $this->page->content->canEdit();
            $this->canDelete = !$this->page->isNewRecord && $this->container->can(AdministerPages::class);
        }

        parent::init();
    }

    protected function setDefaults()
    {
        if($this->edit) {
            $this->blocks = [static::BLOCK_EDIT, static::BLOCK_START];
        } else if($this->page && (!$this->revision || $this->revision->is_latest)) {
            $this->blocks = [static::BLOCK_START, static::BLOCK_PAGE_VIEW, static::BLOCK_BOTTOM];
        } else if($this->page && $this->revision) {
            $this->blocks = [static::BLOCK_START, static::BLOCK_REVISION_VIEW, static::BLOCK_BOTTOM];
        } else {
            $this->blocks = [static::BLOCK_START, static::BLOCK_BOTTOM];
        }
    }

    public function run()
    {
        return $this->render('menu', [
            'blocks' => $this->blocks,
            'cols' => $this->cols
        ]);
    }

    /**
     * @param $link
     * @return Link|string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderLink($link) {
        if(in_array($link, $this->excludes)) {
            return '';
        }

        $link = $this->getLink($link);

        return empty($link) ? '' : $this->renderListItem($link);
    }

    /**
     * @param $link
     * @return Link|string
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    private function getLink($link) {
        switch ($link) {
            case static::LINK_HOME:
                return ($this->home) ? Link::to(Yii::t('WikiModule.base', 'Home'), Url::toWiki($this->home))->icon('fa-home') : null;
            case static::LINK_INDEX:
                return  Link::to(Yii::t('WikiModule.base', 'Index'), Url::toOverview($this->container))->icon('fa-list-alt')->id('wiki_index');
            case static::LINK_EDIT:
                return ($this->canEdit) ? Link::to(Yii::t('WikiModule.base', 'Edit page'), Url::toWikiEdit($this->page))->icon('fa-pencil-square-o edit') : null;
            case static::LINK_HISTORY:
                return ($this->canViewHistory()) ? Link::to(Yii::t('WikiModule.base', 'Page History'), Url::toWikiHistory($this->page))->icon('fa-clock-o history') : null;
            case static::LINK_PERMA:
                return PermaLink::widget(['content' => $this->page->content]);
            case static::LINK_REVERT:
                return ($this->canEdit) ?
                    Link::withAction(Yii::t('WikiModule.base', 'Revert this'), 'wiki.revertRevision', Url::toWikiRevertRevision($this->page, $this->revision))
                        ->icon('fa-history history')->confirm(
                            Yii::t('WikiModule.base', '<strong>Confirm</strong> page reverting'),
                            Yii::t('WikiModule.base', 'Do you really want to revert this page?'),
                            Yii::t('WikiModule.base', 'Revert')) : null;
            case static::LINK_REVERT_GO_BACK:
                return Link::to(Yii::t('WikiModule.base', 'Go back'), Url::toWikiHistory($this->page))->icon('fa-reply');
            case static::LINK_NEW:
                $url = ($this->page && $this->page->is_category)
                    ? Url::toWikiCreate($this->container, $this->page->id)
                    : Url::toWikiCreate($this->container);
                return ($this->canCreatePage()) ? Link::to(Yii::t('WikiModule.base', 'New page'),$url)->icon('fa-plus new') : null;
            case static::LINK_EDIT_DELETE:
                return $this->canDelete
                    ? Link::withAction(Yii::t('WikiModule.base', 'Delete'), 'wiki.delete', Url::toWikiDelete($this->page))
                        ->icon('fa-trash-o delete')
                        ->confirm() : null;
            case static::LINK_EDIT_CANCEL:
                $url = $this->page->isNewRecord ? Url::toOverview($this->container) : Url::toWiki($this->page);
                return Link::to(Yii::t('WikiModule.base', 'Cancel'), $url)->icon('fa-reply')->id('wiki_cancel');
            case static::LINK_BACK_TO_PAGE:
                return Link::to(Yii::t('WikiModule.base', 'Back to page'), Url::toWiki($this->page))->icon('fa-reply');
            case static::LINK_MOVE:
                return (!$this->page->isNewRecord && $this->page->canMove()) ?  MoveContentLink::widget(['model' => $this->page]) : null;
        }
    }

    protected function renderListItem($link)
    {
        return '<li>'.$link.'</li>';
    }

    /**
     * @return boolean can create new wiki site
     * @throws \yii\base\InvalidConfigException
     */
    public function canCreatePage()
    {
        return $this->container->can(CreatePage::class);
    }

    /**
     * @return boolean can view wiki page history?
     * @throws \yii\base\InvalidConfigException
     */
    public function canViewHistory()
    {
        return $this->container->can(ViewHistory::class);
    }

    /**
     * @return boolean can manage wiki sites?
     * @throws \yii\base\InvalidConfigException
     */
    public function canAdminister()
    {
        return $this->container->can(AdministerPages::class);
    }

}