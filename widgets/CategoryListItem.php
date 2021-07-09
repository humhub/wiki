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
use humhub\modules\user\models\User;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use Yii;

class CategoryListItem extends Widget
{
    /**
     * @var WikiPage
     */
    public $category;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var WikiPage[]
     */
    public $pages;

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
     * @var bool|null
     */
    private static $canAdminister = null;

    /**
     * @var bool|null
     */
    public static $canCreate = null;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if($this->showDrag === null) {
            $this->showDrag = $this->canAdminister();
        }

        if($this->showAddPage === null) {
            $this->showAddPage = $this->canCreate();
        }

        if ($this->category) {
            $this->title = $this->category->title;
            $this->pages = $this->category->findChildren()->all();
        }

        return $this->render('categoryListItem', [
            'icon' => $this->icon,
            'title' => $this->title,
            'pages' => $this->pages,
            'hideTitle' => $this->hideTitle,
            'showAddPage' => $this->showAddPage,
            'showDrag' => $this->showDrag,
            'contentContainer' => $this->contentContainer,
            'category' => $this->category,
        ]);
    }

    public static function clear()
    {
        static::$canAdminister = null;
        static::$canCreate = null;
    }

    private function canAdminister()
    {
        if(static::$canAdminister === null) {
            static::$canAdminister =  $this->contentContainer->can(AdministerPages::class);
        }

        return static::$canAdminister;
    }

    private function canCreate()
    {
        if(static::$canCreate === null) {
            static::$canCreate =  $this->contentContainer->can(CreatePage::class);
        }

        return static::$canCreate;
    }

    public function canEdit(WikiPage $page)
    {
        if (Yii::$app->user->isGuest || ($this->contentContainer instanceof User && !$this->contentContainer->isCurrentUser())) {
            return false;
        }

        if ($this->contentContainer->can(AdministerPages::class)) {
            return true;
        }

        if (!$page->admin_only && $this->contentContainer->can(EditPages::class)) {
            return true;
        }

        if (!Yii::$app->user->isGuest && !$page->admin_only && $page->content->created_by === Yii::$app->user->id) {
            return true;
        }

        return false;
    }

}