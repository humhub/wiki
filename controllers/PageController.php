<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */

/**
 * Description of PageController
 *
 * @author luke
 */
class PageController extends ContentContainerController
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function beforeAction($action)
    {
        $this->subLayout = '_layout';
        $this->checkContainerAccess();
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $homePage = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('is_home' => 1));
        if ($homePage !== null) {
            $this->redirect($this->createContainerUrl('view', array('title' => $homePage->title)));
        } else {
            $this->redirect($this->createContainerUrl('list'));
        }
    }

    public function actionList()
    {
        $pages = WikiPage::model()->contentContainer($this->contentContainer)->findAll();
        $this->render('list', array('pages' => $pages));
    }

    public function actionView()
    {
        $title = Yii::app()->request->getQuery('title');
        $revisionId = Yii::app()->request->getQuery('revision', 0);

        $page = WikiPage::model()->findByAttributes(array('title' => $title));
        if ($page !== null) {

            $revision = null;
            if ($revisionId != 0) {
                $revision = WikiPageRevision::model()->findByAttributes(array('wiki_page_id' => $page->id, 'revision' => $revisionId));
            }
            if ($revision == null) {
                $revision = $page->latestRevision;
            }

            $this->render('view', array('page' => $page, 'revision' => $revision));
        } else {
            $this->redirect($this->createContainerUrl('edit', array('title' => $title)));
        }
    }

    public function actionEdit()
    {
        $title = Yii::app()->request->getQuery('title');

        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('title' => $title));
        if ($page === null) {
            $page = new WikiPage();
            $page->title = $title;
            $page->content->setContainer($this->contentContainer);
        }
        $revision = $page->createRevision();

        if (isset($_POST['WikiPage']) && isset($_POST['WikiPageRevision'])) {
            $page->content->container = $this->contentContainer;

            $page->attributes = $_POST['WikiPage'];
            $revision->attributes = $_POST['WikiPageRevision'];


            if ($page->validate()) {
                $page->save();

                $revision->wiki_page_id = $page->id;
                if ($revision->validate()) {
                    $revision->save();
                    $this->redirect($this->createContainerUrl('view', array('title' => $page->title)));
                }
            }
        }

        $this->render('edit', array('page' => $page, 'revision' => $revision));
    }

    public function actionHistory()
    {
        $id = Yii::app()->request->getQuery('id');

        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByPk($id);

        if ($page === null) {
            throw new CHttpException(404, 'Page not found!');
        }

        $this->render('history', array('page' => $page));
    }

    public function actionDelete()
    {

        $this->forcePostRequest();

        $id = Yii::app()->request->getQuery('id');
        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByPk($id);

        if ($page === null) {
            throw new CHttpException(404, 'Page not found!');
        }

        $page->delete();

        $this->redirect($this->createContainerUrl('index'));
    }

}
