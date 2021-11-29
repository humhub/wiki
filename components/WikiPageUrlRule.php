<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\components;

use humhub\components\ContentContainerUrlRuleInterface;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use yii\base\Component;
use yii\web\UrlManager;
use yii\web\UrlRuleInterface;

/**
 * Wiki Pages URL Rule
 *
 * @author luke
 */
class WikiPageUrlRule extends Component implements UrlRuleInterface, ContentContainerUrlRuleInterface
{

    /**
     * @inheritdoc
     */
    public function parseContentContainerRequest(ContentContainerActiveRecord $container, UrlManager $manager, string $containerUrlPath, array $urlParams)
    {
        if (substr($containerUrlPath, 0, 5) == 'wiki/') {
            $parts = explode('/', $containerUrlPath, 2);
            if (isset($parts[1]) && strpos($parts[1], '/') === false) {
                /* @var $wikiPage WikiPage */
                $wikiPage = WikiPage::find()
                    ->leftJoin('content', 'content.object_model = :wikiPageModel AND content.object_id = wiki_page.id', [':wikiPageModel' => WikiPage::class])
                    ->where(['content.contentcontainer_id' => $container->contentcontainer_id])
                    ->andWhere(['wiki_page.title' => $parts[1]])
                    ->one();

                if ($wikiPage !== null) {
                    $urlParams['title'] = $wikiPage->title;
                    return ['wiki/page/view', $urlParams];
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function createContentContainerUrl(UrlManager $manager, string $containerUrlPath, string $route, array $params)
    {
        if ($route === 'wiki/page/view' && isset($params['title'])) {
            $url = $containerUrlPath . '/wiki/' . urlencode($params['title']);
            unset($params['title']);

            if (!empty($params) && ($query = http_build_query($params)) !== '') {
                $url .= '?' . $query;
            }
            return $url;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        return false;
    }

}
