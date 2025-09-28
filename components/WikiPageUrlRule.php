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
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\BaseInflector;
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
     * @throws Exception
     */
    public function parseContentContainerRequest(ContentContainerActiveRecord $container, UrlManager $manager, string $containerUrlPath, array $urlParams)
    {
        if (strpos($containerUrlPath, 'wiki/') === 0) {
            $parts = explode('/', $containerUrlPath);
            if (empty($parts[1])) {
                return false;
            }

            $wikiPage = null;
            $query = WikiPage::find()->contentContainer($container);
            $id = (int)$parts[1];

            if ((string)$id === $parts[1]) { // the value after wiki/ doesn't contain other char than numbers
                $wikiPage = $query->andWhere(['wiki_page.id' => $id])->one();
            } else {
                if (count($parts) === 2) { // Fallback for old URLs without ID
                    $title = $parts[1];
                } else { // Fallback for old URLs with `/page/view?title=`
                    $title = Yii::$app->request->get('title');
                }
                if ($title) {
                    /* @var $wikiPage WikiPage */
                    $wikiPage = $query->andWhere(['wiki_page.title' => $title])->one();
                }
            }

            if ($wikiPage !== null) {
                $urlParams['id'] = $wikiPage->id;
                return ['wiki/page/view', $urlParams];
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function createContentContainerUrl(UrlManager $manager, string $containerUrlPath, string $route, array $params)
    {
        if ($route === 'wiki/page/view' && isset($params['id'])) {
            $url = $containerUrlPath . '/wiki/' . $params['id'];

            if (!empty($params['title'])) {
                $url .= '/' . BaseInflector::slug($params['title']);
            }

            unset($params['id'], $params['title']);

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
