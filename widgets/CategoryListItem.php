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
    public $url;

    /**
     * @var WikiPage[]
     */
    public $pages;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var bool
     */
    public $hideTitle = false;

    public $icon = 'fa-caret-square-o-down';

    /**
     * @inheritdoc
     */
    public function run()
    {
        if($this->category) {
            $this->title = $this->category->title;
            $this->url = $this->category->getUrl();
            $this->pages = $this->category->findChildren()->all();

            if($this->contentContainer) {
                $this->editUrl = $this->contentContainer->createUrl('/wiki/page/edit', ['id' => $this->category->id]);
            }
        }

        return $this->render('categoryListItem', [
            'icon' => $this->icon,
            'title' => $this->title,
            'url' => $this->url,
            'pages' => $this->pages,
            'hideTitle' => $this->hideTitle,
            'editUrl' => $this->editUrl,
            'contentContainer' => $this->contentContainer,
            'category' => $this->category
        ]);
    }

    public function canEdit(WikiPage $page)
    {
        if(Yii::$app->user->isGuest || ($this->contentContainer instanceof User && !$this->contentContainer->isCurrentUser())) {
            return false;
        }

        if($this->contentContainer->can(AdministerPages::class)) {
            return true;
        }

        if(!$page->admin_only && $this->contentContainer->can(EditPages::class)) {
            return true;
        }

        if(!Yii::$app->user->isGuest && !$page->admin_only && $page->content->created_by === Yii::$app->user->id) {
            return true;
        }

        return false;
    }


}