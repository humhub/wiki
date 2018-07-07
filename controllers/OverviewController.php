<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\controllers;

use humhub\modules\wiki\models\WikiPage;
use Yii;
use yii\db\Expression;


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
            return $this->redirect($this->contentContainer->createUrl('/wiki/page/view', ['title' => $homePage->title]));
        }

        if ($this->hasCategoryPages()) {
            return $this->redirect($this->contentContainer->createUrl('/wiki/overview/list-categories'));
        }

        return $this->redirect($this->contentContainer->createUrl('/wiki/overview/list'));
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

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        return $this->render('list', array(
            'pages' => $query->all(),
            'pagination' => $pagination,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'hasCategories' => $this->hasCategoryPages(),
        ));
    }


    public function actionListCategories()
    {

        if (!$this->hasCategoryPages()) {
            return $this->redirect($this->contentContainer->createUrl('/wiki/overview/list'));
        }

        // Get created categories
        $query = WikiPage::find()
            ->contentContainer($this->contentContainer)
            ->orderBy('title ASC')
            ->andWhere(['wiki_page.is_category' => 1]);

        return $this->render('list-categories', array(
            'categories' => $query->all(),
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'categoryPageLimit' => 5,
            'pagesWithoutCategoryQuery' => WikiPage::find()->contentContainer($this->contentContainer)
            	->andWhere(['IS', 'parent_page_id', new Expression('NULL')])
            	->andWhere(['wiki_page.is_category' => 0]),
        ));

    }
}