<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\controllers;

use humhub\modules\content\search\SearchRequest;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class OverviewController
 * @package humhub\modules\wiki\controllers
 */
class OverviewController extends BaseController
{
    /**
     * @return $this|void|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $homePage = $this->getHomePage();
        if ($homePage !== null) {
            return $this->redirect(Url::toWiki($homePage));
        }

        return $this->redirect(Url::toOverview($this->contentContainer));
    }


    /**
     * @return OverviewController|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionListCategories($hideSidebarOnSmallScreen = false)
    {
        if (!$this->hasPages()) {
            return $this->render('no-pages', [
                'canCreatePage' => $this->canCreatePage(),
                'createPageUrl' => $this->contentContainer->createUrl('/wiki/page/edit'),
                'contentContainer' => $this->contentContainer,
            ]);
        }

        $views = ['last-edited'];
        if (!$hideSidebarOnSmallScreen) {
            array_unshift($views, 'list-categories');
        }

        return $this->renderSidebarContent($views, [
            'contentContainer' => $this->contentContainer,
            'canCreate' => $this->canCreatePage(),
            'dataProvider' => $this->getLastEditedDataProvider(),
            'hideSidebarOnSmallScreen' => $hideSidebarOnSmallScreen,
        ]);
    }

    public function actionLastEdited()
    {
        return $this->actionListCategories(true);
    }

    private function getLastEditedDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => WikiPage::find()
                ->contentContainer($this->contentContainer)
                ->readable(),
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'attributes' => [
                    'title',
                    'updated_at' => [
                        'asc' => ['content.updated_at' => SORT_ASC],
                        'desc' => ['content.updated_at' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);
    }

    public function actionSearch($keyword)
    {
        $searchRequest = new SearchRequest([
            'contentType' => WikiPage::class,
            'contentContainer' => [$this->contentContainer->guid],
            'pageSize' => 10,
        ]);

        if ($searchRequest->load(Yii::$app->request->get(), '') && $searchRequest->validate()) {
            $resultSet = Yii::$app->getModule('content')->getSearchDriver()->searchCached($searchRequest, 30);
        } else {
            $resultSet = null;
        }

        return $this->renderSidebarContent('search', [
            'contentContainer' => $this->contentContainer,
            'resultSet' => $resultSet,
        ]);
    }

    public function actionUpdateFoldingState(int $categoryId, int $state)
    {
        $this->updateFoldingState($categoryId, $state);
    }
}
