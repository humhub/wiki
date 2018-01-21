<?php

namespace humhub\modules\wiki\controllers;

use Yii;
use yii\web\HttpException;
use humhub\widgets\MarkdownView;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\file\models\File;
use humhub\modules\content\models\Content;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;

/**
 * PageController
 *
 * @author luke
 */
class PageController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
    public $hideSidebar = true;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if ($this->contentContainer instanceof Space && !$this->contentContainer->isMember()) {
                throw new HttpException(403, Yii::t('WikiModule.base', 'You need to be member of the space "%space_name%" to access this wiki page!', ['%space_name%' => $this->contentContainer->name]));
            }
            return true;
        }

        return false;
    }

    public function actionIndex()
    {
        $homePage = $this->getHomePage();

        if ($homePage !== null) {
            return $this->redirect($this->contentContainer->createUrl('/wiki/page/view', ['title' => $homePage->title]));
        }

        return $this->redirect($this->contentContainer->createUrl('/wiki/page/list'));
    }

    public function actionList()
    {
        $pageSize = 30;
        $query = WikiPage::find()->orderBy('title ASC')->contentContainer($this->contentContainer);
        $countQuery = clone $query;

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        return $this->render('list', [
                    'pages' => $query->all(),
                    'pagination' => $pagination,
                    'homePage' => $this->getHomePage(),
                    'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionView()
    {
        $title = Yii::$app->request->get('title');
        $revisionId = Yii::$app->request->get('revision', 0);

        $page = WikiPage::find()->contentContainer($this->contentContainer)->where(['title' => $title])->one();
        if ($page !== null) {
            $revision = null;
            if ($revisionId != 0) {
                $revision = WikiPageRevision::findOne(['wiki_page_id' => $page->id, 'revision' => $revisionId]);
            }
            if ($revision == null) {
                $revision = $page->latestRevision;

                // There is no revision for this page.
                if ($revision == null) {
                    // Delete page without revision
                    $page->delete();

                    // Forward to edit
                    return $this->redirect($this->contentContainer->createUrl('edit', ['title' => $page->title]));
                }
            }
            return $this->render('view', [
                        'page' => $page,
                        'revision' => $revision,
                        'homePage' => $this->getHomePage(),
                        'contentContainer' => $this->contentContainer,
                        'content' => $revision->content,
                        'canViewHistory' => $this->canViewHistory()
            ]);
        } else {
            return $this->redirect($this->contentContainer->createUrl('edit', ['title' => $title]));
        }
    }

    public function actionEdit()
    {
        $id = (int) Yii::$app->request->get('id');

        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();
        if ($page === null) {
            if (!$this->canCreatePage()) {
                throw new HttpException(403, Yii::t('WikiModule.base', 'Page creation disabled!'));
            }

            $page = new WikiPage();
            $page->content->setContainer($this->contentContainer);
            $page->content->visibility = Content::VISIBILITY_PRIVATE;
            $page->title = Yii::$app->request->get('title');
            $page->scenario = 'create';
        } elseif (!$this->canEdit($page)) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Page not editable!'));
        }

        if ($this->canAdminister()) {
            $page->scenario = 'admin';
        }

        $revision = $page->createRevision();

        if ($page->load(Yii::$app->request->post()) && $revision->load(Yii::$app->request->post())) {
            $page->content->container = $this->contentContainer;
            if ($page->validate()) {
                $page->save();
                File::attachPrecreated($page, Yii::$app->request->post('fileUploaderHiddenGuidField'));
                $revision->wiki_page_id = $page->id;
                if ($revision->validate()) {
                    $revision->save();
                    return $this->redirect($this->contentContainer->createUrl('view', ['title' => $page->title]));
                }
            }
        }

        return $this->render('edit', [
                    'page' => $page,
                    'revision' => $revision,
                    'homePage' => $this->getHomePage(),
                    'contentContainer' => $this->contentContainer
        ]);
    }

    public function actionHistory()
    {
        if (!$this->canViewHistory()) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Permission denied. You have no rights to view the history.'));
        }
        
        $id = Yii::$app->request->get('id');

        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();

        if ($page === null) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        $query = WikiPageRevision::find();
        $query->orderBy('wiki_page_revision.id DESC');
        $query->where(['wiki_page_id' => $page->id]);
        $query->joinWith('author');

        $countQuery = clone $query;

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => '20']);
        $query->offset($pagination->offset)->limit($pagination->limit);


        return $this->render('history', [
                    'page' => $page,
                    'revisions' => $query->all(),
                    'pagination' => $pagination,
                    'homePage' => $this->getHomePage(),
                    'contentContainer' => $this->contentContainer]);
    }

    public function actionDelete()
    {
        $this->forcePostRequest();

        $id = Yii::$app->request->get('id');
        $page = WikiPage::find()->contentContainer($this->contentContainer)->where(['wiki_page.id' => $id])->one();

        if ($page === null) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        if (!$this->canAdminister()) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Permission denied. You have no administration rights.'));
        }
        $page->delete();

        return $this->redirect($this->contentContainer->createUrl('index'));
    }

    public function actionRevert()
    {
        $this->forcePostRequest();

        $id = (int) Yii::$app->request->get('id');
        $toRevision = (int) Yii::$app->request->get('toRevision');

        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();

        if ($page === null) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        if (!$this->canEdit($page)) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Page not editable!'));
        }

        $revision = WikiPageRevision::findOne([
                    'revision' => $toRevision,
                    'wiki_page_id' => $page->id
        ]);

        if ($revision->is_latest) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Revert not possible. Already latest revision!'));
        }

        $revertedRevision = $page->createRevision();
        $revertedRevision->content = $revision->content;
        $revertedRevision->save();

        return $this->redirect($this->contentContainer->createUrl('view', ['title' => $page->title]));
    }

    /**
     * Markdown preview action for MarkdownViewWidget
     * We require an own preview action here to also handle internal wiki links.
     */
    public function actionPreviewMarkdown()
    {
        $this->forcePostRequest();
        $content = MarkdownView::widget(['markdown' => Yii::$app->request->post('markdown'), 'parserClass' => 'humhub\modules\wiki\Markdown']);

        return $this->renderAjaxContent($content);
    }

    /**
     * @return WikiPage the homepage
     */
    private function getHomePage()
    {
        return WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['is_home' => 1])->one();
    }

    /**
     * @return boolean can manage wiki sites?
     */
    public function canAdminister()
    {
        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\AdministerPages());
    }

    /**
     * @param WikiPage $page
     * @return boolean can edit given wiki site?
     */
    public function canEdit($page)
    {
        if ($page->admin_only) {
            return $this->canAdminister();
        }

        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\EditPages());
    }

    /**
     * @return boolean can create new wiki site
     */
    public function canCreatePage()
    {
        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\CreatePage());
    }
    
    /**
     * @return boolean can view wiki page history?
     */
    public function canViewHistory()
    {
        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\ViewHistory());
    }
}
