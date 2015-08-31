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
        if ($this->contentContainer instanceof Space && !$this->contentContainer->isMember()) {
            throw new HttpException(403, 'You need to be member of this space to this wiki!');
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $homePage = $this->getHomePage();

        if ($homePage !== null) {
            return $this->redirect($this->contentContainer->createUrl('/wiki/page/view', array('title' => $homePage->title)));
        }

        return $this->redirect($this->contentContainer->createUrl('/wiki/page/list'));
    }

    public function actionList()
    {
        $pageSize = 100;
        $query = WikiPage::find()->contentContainer($this->contentContainer);
        $countQuery = clone $query;

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        return $this->render('list', array(
                    'pages' => $query->all(),
                    'pagination' => $pagination,
                    'homePage' => $this->getHomePage(),
                    'contentContainer' => $this->contentContainer,
        ));
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
            }
            return $this->render('view', [
                        'page' => $page,
                        'revision' => $revision,
                        'homePage' => $this->getHomePage(),
                        'contentContainer' => $this->contentContainer,
                        'content' => $revision->content
            ]);
        } else {
            return $this->redirect($this->contentContainer->createUrl('edit', array('title' => $title)));
        }
    }

    public function actionEdit()
    {
        $id = (int) Yii::$app->request->get('id');

        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();
        if ($page === null) {
            $page = new WikiPage();
            $page->content->setContainer($this->contentContainer);
            $page->content->visibility = Content::VISIBILITY_PRIVATE;
            $page->title = Yii::$app->request->get('title');
        }

        if ($page->admin_only && !$page->canAdminister()) {
            throw new HttpException(403, 'Page not editable!');
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
                    return $this->redirect($this->contentContainer->createUrl('view', array('title' => $page->title)));
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
        $id = Yii::$app->request->get('id');

        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();

        if ($page === null) {
            throw new HttpException(404, 'Page not found!');
        }

        $query = WikiPageRevision::find();
        $query->orderBy('wiki_page_revision.id DESC');
        $query->where(['wiki_page_id' => $page->id]);
        $query->joinWith('author');

        $countQuery = clone $query;

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => "20"]);
        $query->offset($pagination->offset)->limit($pagination->limit);


        return $this->render('history', array(
                    'page' => $page,
                    'revisions' => $query->all(),
                    'pagination' => $pagination,
                    'homePage' => $this->getHomePage(),
                    'contentContainer' => $this->contentContainer)
        );
    }

    public function actionDelete()
    {
        $this->forcePostRequest();

        $id = Yii::$app->request->get('id');
        $page = WikiPage::find()->contentContainer($this->contentContainer)->where(['wiki_page.id' => $id])->one();

        if ($page === null) {
            throw new HttpException(404, 'Page not found!');
        }

        if ($page->canAdminister()) {
            $page->delete();
        }

        return $this->redirect($this->contentContainer->createUrl('index'));
    }

    public function actionRevert()
    {
        $this->forcePostRequest();

        $id = (int) Yii::$app->request->get('id');
        $toRevision = (int) Yii::$app->request->get('toRevision');

        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();

        if ($page === null) {
            throw new HttpException(404, 'Page not found!');
        }

        if ($page->admin_only && !$page->canAdminister()) {
            throw new HttpException(403, 'Page not editable!');
        }

        $revision = WikiPageRevision::findOne(array(
                    'revision' => $toRevision,
                    'wiki_page_id' => $page->id
        ));

        if ($revision->is_latest) {
            throw new HttpException(404, 'Already latest revision!');
        }

        $revertedRevision = $page->createRevision();
        $revertedRevision->content = $revision->content;
        $revertedRevision->save();

        return $this->redirect($this->contentContainer->createUrl('view', array('title' => $page->title)));
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

    private function getHomePage()
    {
        return WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['is_home' => 1])->one();
        ;
    }

}
