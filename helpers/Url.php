<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\helpers;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;


/**
 * Class Url
 */
class Url extends \yii\helpers\Url
{
    const ROUTE_HOME = '/wiki/overview/index';
    const ROUTE_OVERVIEW = '/wiki/overview/list-categories';
    const ROUTE_WIKI_PAGE = '/wiki/page/view';
    const ROUTE_WIKI_EDIT = '/wiki/page/edit';
    const ROUTE_WIKI_HISTORY = '/wiki/page/history';
    const ROUTE_WIKI_REVERT = '/wiki/page/revert';

    public static function toHome(ContentContainerActiveRecord $container)
    {
        return static::to([static::ROUTE_OVERVIEW, 'container' => $container]);
    }

    public static function toOverview(ContentContainerActiveRecord $container)
    {
        return static::to([static::ROUTE_OVERVIEW, 'container' => $container]);
    }

    public static function toWikiHistory(WikiPage $page)
    {
        return static::to([static::ROUTE_WIKI_HISTORY, 'id' => $page->id, 'container' => $page->content->container]);
    }

    public static function toWikiRevertRevision(WikiPage $page, WikiPageRevision $revision)
    {
        return static::to([static::ROUTE_WIKI_REVERT, 'id' => $page->id, 'toRevision' => $revision->revision, 'container' => $page->content->container]);
    }

    public static function toWikiCreate(ContentContainerActiveRecord $container)
    {
        return static::wikiEdit($container);
    }

    public static function toWikiEdit(WikiPage $page)
    {
        return static::wikiEdit($page->content->container, $page->id);
    }

    private static function wikiEdit(ContentContainerActiveRecord $container, $id = null)
    {
        return static::to([static::ROUTE_WIKI_EDIT, 'id' => $id, 'container' => $container]);
    }

    /**
     * @param WikiPage $page
     * @param null $revision
     * @return string
     */
    public static function toWiki(WikiPage $page, $revision = null)
    {
        return static::to([static::ROUTE_WIKI_PAGE, 'title' => $page->title, 'revision' => $revision, 'container' => $page->content->container]);
    }

    public static function toWikiByTitle($title, ContentContainerActiveRecord $container,  $revision = null)
    {
        return static::to([static::ROUTE_WIKI_PAGE, 'title' => $title, 'revision' => $revision, 'container' => $container]);
    }

}