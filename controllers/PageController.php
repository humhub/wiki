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
        $this->checkContainerAccess();

        if ($this->contentContainer instanceof Space) {
            $this->subLayout = '_layout_space';
        }

        if ($this->contentContainer instanceof User) {
            $this->subLayout = '_layout_user';
        }

        $assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__) . '/../assets', true, 0, defined('YII_DEBUG'));
        Yii::app()->clientScript->registerCssFile($assetPrefix . '/wiki.css');

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
        $criteria = new CDbCriteria();
        $criteria->order = 'id DESC';

        $homePage = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('is_home' => 1));
        $pageCount = WikiPage::model()->contentContainer($this->contentContainer)->count($criteria);

        $pagination = new CPagination($pageCount);
        $pagination->setPageSize(50);
        $pagination->applyLimit($criteria);

        $pages = WikiPage::model()->contentContainer($this->contentContainer)->findAll($criteria);

        $this->render('list', array('pages' => $pages, 'pagination' => $pagination, 'homePage' => $homePage));
    }

    public function actionView()
    {
        $title = Yii::app()->request->getQuery('title');
        $revisionId = Yii::app()->request->getQuery('revision', 0);
        $homePage = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('is_home' => 1));

        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('title' => $title));
        if ($page !== null) {

            $revision = null;
            if ($revisionId != 0) {
                $revision = WikiPageRevision::model()->findByAttributes(array('wiki_page_id' => $page->id, 'revision' => $revisionId));
            }
            if ($revision == null) {
                $revision = $page->latestRevision;
            }
            $this->render('view', array('page' => $page, 'revision' => $revision, 'homePage' => $homePage, 'content' => $revision->content));
        } else {
            $this->redirect($this->createContainerUrl('edit', array('title' => $title)));
        }
    }

    public function actionEdit()
    {
        //$assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__) . '/../assets', true, 0, defined('YII_DEBUG'));
        //Yii::app()->clientScript->registerCssFile($assetPrefix . '/bootstrap-markdown-override.css');

        $id = (int) Yii::app()->request->getQuery('id');
        $homePage = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('is_home' => 1));

        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('id' => $id));
        if ($page === null) {
            $page = new WikiPage();
            $page->content->setContainer($this->contentContainer);
            $page->content->visibility = Content::VISIBILITY_PRIVATE;
            $page->title = Yii::app()->request->getParam('title');
        }

        if ($page->admin_only && !$page->canAdminister()) {
            throw new CHttpException(403, 'Page not editable!');
        }

        $revision = $page->createRevision();

        if (isset($_POST['WikiPage']) && isset($_POST['WikiPageRevision'])) {
            $page->content->container = $this->contentContainer;

            $page->attributes = $_POST['WikiPage'];
            $revision->attributes = $_POST['WikiPageRevision'];

            if ($page->validate()) {
                $page->save();

                File::attachPrecreated($page, Yii::app()->request->getParam('fileUploaderHiddenGuidField'));


                $revision->wiki_page_id = $page->id;
                if ($revision->validate()) {
                    $revision->save();
                    $this->redirect($this->createContainerUrl('view', array('title' => $page->title)));
                }
            }
        }

        $this->render('edit', array('page' => $page, 'revision' => $revision, 'homePage' => $homePage));
    }

    public function actionHistory()
    {
        $id = Yii::app()->request->getQuery('id');
        $homePage = WikiPage::model()->contentContainer($this->contentContainer)->findByAttributes(array('is_home' => 1));

        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByPk($id);

        if ($page === null) {
            throw new CHttpException(404, 'Page not found!');
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'id DESC';
        $criteria->condition = 'wiki_page_id=:pageId';
        $criteria->params = array(':pageId' => $page->id);

        $revisionCount = WikiPageRevision::model()->count($criteria);

        $pagination = new CPagination($revisionCount);
        $pagination->setPageSize(50);
        $pagination->applyLimit($criteria);

        $revisions = WikiPageRevision::model()->findAll($criteria);

        $this->render('history', array('page' => $page, 'revisions' => $revisions, 'pagination' => $pagination, 'homePage' => $homePage));
    }

    public function actionDelete()
    {
        $this->forcePostRequest();

        $id = Yii::app()->request->getQuery('id');
        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByPk($id);

        if ($page === null) {
            throw new CHttpException(404, 'Page not found!');
        }

        if ($page->canAdminister()) {
            $page->delete();
        }

        $this->redirect($this->createContainerUrl('index'));
    }

    public function actionRevert()
    {
        $this->forcePostRequest();

        $id = (int) Yii::app()->request->getQuery('id');
        $toRevision = (int) Yii::app()->request->getQuery('toRevision');
        $page = WikiPage::model()->contentContainer($this->contentContainer)->findByPk($id);

        if ($page === null) {
            throw new CHttpException(404, 'Page not found!');
        }

        if ($page->admin_only && !$page->canAdminister()) {
            throw new CHttpException(403, 'Page not editable!');
        }

        $revision = WikiPageRevision::model()->findByAttributes(array(
            'revision' => $toRevision,
            'wiki_page_id' => $page->id
        ));

        if ($revision->is_latest) {
            throw new CHttpException(404, 'Already latest revision!');
        }

        $revertedRevision = $page->createRevision();
        $revertedRevision->content = $revision->content;
        $revertedRevision->save();

        $this->redirect($this->createContainerUrl('view', array('title' => $page->title)));
    }

    /**
     * Markdown preview action for MarkdownViewWidget
     * We require an own preview action here to also handle internal wiki links.
     */
    public function actionPreviewMarkdown()
    {
        $this->forcePostRequest();
        return $this->widget('application.widgets.MarkdownViewWidget', array('markdown' => Yii::app()->request->getParam('markdown'), 'parserClass' => 'WikiMarkdownParser'));
    }

}
