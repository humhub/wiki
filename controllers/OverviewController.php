<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\controllers;

use Yii;
use yii\data\Pagination;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;


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
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionList()
    {
        if (!$this->hasPages()) {
            return $this->render('no-pages', [
                'canCreatePage' => $this->canCreatePage(),
                'createPageUrl' => $this->contentContainer->createUrl('/wiki/page/edit')
            ]);
        }

        $pageSize = Yii::$app->getModule('wiki')->pageSize;

        $query = WikiPage::find()
            ->contentContainer($this->contentContainer)
            ->orderBy('title ASC');

        $countQuery = clone $query;

        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        return $this->render('list', [
            'pages' => $query->all(),
            'pagination' => $pagination,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'hasCategories' => $this->hasCategoryPages(),
        ]);
    }


    /**
     * @return OverviewController|string|\yii\console\Response|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionListCategories()
    {

        if (!$this->hasCategoryPages()) {
            return $this->redirect($this->contentContainer->createUrl('/wiki/overview/list'));
        }

        return $this->render('list-categories', [
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'canCreate' => $this->canCreatePage()
        ]);

    }
}