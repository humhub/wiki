<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\helpers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;


/**
 * Class Url
 */
class Url extends \yii\helpers\Url
{
    const ROUTE_HOME = '/wiki/overview/index';
    const ROUTE_OVERVIEW = '/wiki/overview/list-categories';
    const ROUTE_WIKI_PAGE = '/wiki/page/view';

    public static function toHome(ContentContainerActiveRecord $container)
    {
        return static::to([static::ROUTE_OVERVIEW, 'container' => $container]);
    }

    public static function toOverview(ContentContainerActiveRecord $container)
    {
        return static::to([static::ROUTE_OVERVIEW, 'container' => $container]);
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