<?php

namespace humhub\modules\wiki\controllers;

use humhub\components\access\ControllerAccess;
use humhub\libs\Html;
use humhub\modules\wiki\helpers\HeadlineExtractor;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\DefaultSettings;
use humhub\modules\wiki\models\forms\PageEditForm;
use humhub\modules\wiki\models\forms\WikiPageItemDrop;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\wiki\permissions\ViewHistory;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\web\HttpException;
use yii\web\Response;

/**
 * PageController
 *
 * @author luke
 */
class PageController extends BaseController
{
    /**
     * @return $this|Response
     * @throws Exception
     */
    public function actionIndex()
    {
        return $this->redirect($this->contentContainer->createUrl('/wiki/overview'));
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionList()
    {
        return $this->redirect($this->contentContainer->createUrl('/wiki/overview/list-categories'));
    }

    /**
     * @return string|Response
     * @throws Exception
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionView(int $id = null, $revisionId = null)
    {
        $page = $this->getWikiPage($id);
        if (!$page) {
            throw new HttpException(404, 'Wiki page not found!');
        }

        if ($page->isCategory) {
            // Unfold category on view it
            $this->updateFoldingState($page->id, 0);
        }

        $revision = $this->getRevision($page, $revisionId);
        if (!$revision) {
            $page->delete();
            throw new HttpException(404, 'Wiki page revision not found!');
        }

        return $this->renderSidebarContent('view', [
            'page' => $page,
            'revision' => $revision,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'content' => $revision->content,
            'canViewHistory' => $this->canViewHistory(),
            'canEdit' => $page->canEditContent(),
            'canAdminister' => $this->canAdminister(),
            'canCreatePage' => $this->canCreatePage(),
        ]);
    }

    /**
     * @param int $id
     * @return WikiPage|null
     * @throws Exception
     * @throws Throwable
     */
    private function getWikiPage($id): ?WikiPage
    {
        if (!is_int($id)) {
            return null;
        }

        /** @var WikiPage $wikiPage */
        $wikiPage = WikiPage::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->andWhere(['wiki_page.id' => $id])
            ->one();

        if ($wikiPage) {
            $settings = new DefaultSettings(['contentContainer' => $this->contentContainer]);
            $this->view->setPageTitle(Html::encode($settings->module_label), true);
            $this->view->setPageTitle($wikiPage->title, true);
            $this->view->meta->setContent($wikiPage);
            $this->view->meta->setImages($wikiPage->fileManager->findAll());
        }

        return $wikiPage;
    }

    /**
     * Returns a revision for the given page, either by a given revisionid or the latest.
     *
     * @param WikiPage $page
     * @param int|null $revisionId
     * @return WikiPageRevision|null
     */
    private function getRevision(WikiPage $page, $revisionId = null)
    {
        $revision = null;
        if ($revisionId != null) {
            $revision = WikiPageRevision::findOne(['wiki_page_id' => $page->id, 'revision' => $revisionId]);
        }

        if (!$revision) {
            $revision = $page->latestRevision;
        }

        return $revision;
    }

    /**
     * Compare two revisions of a Wiki page
     *
     * @param int $id Wiki page ID
     * @param int $revision1 Id of revision 1
     * @param int $revision2 Id of revision 2
     * @return string
     * @throws Exception
     * @throws HttpException
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws StaleObjectException
     */
    public function actionDiff(int $id, int $revision1, int $revision2)
    {
        $page = $this->getWikiPage($id);

        if (!$page) {
            throw new HttpException(404, 'Wiki page not found!');
        }

        $revision1 = $this->getRevision($page, $revision1);
        if (!$revision1) {
            $page->delete();
            throw new HttpException(404, 'Wiki page revision 1 not found!');
        }

        $revision2 = $this->getRevision($page, $revision2);
        if (!$revision2) {
            $page->delete();
            throw new HttpException(404, 'Wiki page revision 2 not found!');
        }

        return $this->renderSidebarContent('diff', [
            'page' => $page,
            'revision1' => $revision1,
            'revision2' => $revision2,
        ]);
    }

    /**
     * @return $this|string|Response
     * @throws HttpException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function actionEdit($id = null, $title = null, $categoryId = null)
    {
        $form = (new PageEditForm(['container' => $this->contentContainer]))->forPage($id, $title, $categoryId);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            $this->view->saved();
            return $this->redirect(Url::toWiki($form->page));
        }

        $params = [
            'model' => $form,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'canAdminister' => $this->canAdminister(),
            'requireConfirmation' => $form->hasErrors('confirmOverwriting'),
            'displayFieldCategory' => !$form->page->isNewRecord || !$form->page->categoryPage,
        ];

        if ($params['requireConfirmation']) {
            $originalPage = WikiPage::findOne(['id' => $form->page->id]);

            $params = array_merge($params, [
                'diffUrl' => Url::toWikiDiffEditing($originalPage),
                'discardChangesUrl' => $originalPage->getUrl(),
            ]);
        }

        return $this->renderSidebarContent('edit', $params);
    }

    /**
     * Compare the latest and the editing revisions of a Wiki page
     *
     * @param int $id Wiki page ID
     * @return string
     */
    public function actionDiffEditing(int $id)
    {
        $page = $this->getWikiPage($id);

        if (!$page) {
            throw new HttpException(404, 'Wiki page not found!');
        }

        $form = (new PageEditForm(['container' => $this->contentContainer]))->forPage($id);

        if (!$form->load(Yii::$app->request->post())) {
            throw new HttpException(404);
        }

        $submittedRevision = new WikiPageRevision();
        $submittedRevision->revision = time();
        $submittedRevision->content = $form->revision->content;
        $submittedRevision->isCurrentlyEditing = true;

        return $this->render('diff', [
            'page' => $page,
            'revision1' => $page->latestRevision,
            'revision2' => $submittedRevision,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     * @throws HttpException
     * @throws Throwable
     */
    public function actionHeadlines($id)
    {
        if (intval($id) === 0) {
            return $this->asJson([]);
        }

        $page = $this->getWikiPage($id);

        if (!$page) {
            return $this->asJson([]);
        }

        return $this->asJson(HeadlineExtractor::extract($page->latestRevision->content));
    }

    public function actionSort()
    {
        $dropModel = new WikiPageItemDrop(['contentContainer' => $this->contentContainer]);
        if ($dropModel->load(Yii::$app->request->post()) && $dropModel->save()) {
            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['success' => false]);
    }

    /**
     * @return string
     * @throws HttpException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionHistory(int $id)
    {
        if (!$this->canViewHistory()) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Permission denied. You have no rights to view the history.'));
        }

        $page = $this->getWikiPage($id);

        if ($page === null) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        $query = WikiPageRevision::find();
        $query->orderBy('wiki_page_revision.id DESC');
        $query->where(['wiki_page_id' => $page->id]);
        $query->joinWith('author');

        $countQuery = clone $query;

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => "20"]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        $revisions = $query->all();

        return $this->renderSidebarContent('history', [
            'page' => $page,
            'revisions' => $revisions,
            'pagination' => $pagination,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'isEnabledDiffTool' => count($revisions) > 1,
        ]);
    }

    /**
     * @return $this|Response
     * @throws HttpException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(int $id)
    {
        $page = $this->getWikiPage($id);

        if (!$page) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        $page->delete();

        return $this->redirect($this->contentContainer->createUrl('index'));
    }

    /**
     * @param int $id
     * @param int $toRevision
     * @return $this|Response
     * @throws Exception
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function actionRevert(int $id, $toRevision)
    {
        $page = $this->getWikiPage($id);

        if (!$page) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        if (!$page->canEditContent()) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Page not editable!'));
        }

        $revision = WikiPageRevision::findOne([
            'revision' => $toRevision,
            'wiki_page_id' => $page->id,
        ]);

        if (!$revision) {
            throw new HttpException(404, 'Revision not found!');
        }

        if ($revision->is_latest) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Revert not possible. Already latest revision!'));
        }

        $revertedRevision = $page->createRevision();
        $revertedRevision->content = $revision->content;
        $revertedRevision->save();

        return $this->asJson([
            'success' => 1,
            'redirect' => Url::toWiki($page),
        ]);
    }

    public function actionPickerSearch($keyword, $id = null)
    {
        $pages = WikiPage::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->andWhere(['like', 'wiki_page.title', $keyword]);
        if ($id) {
            $pages->andWhere(['!=', 'wiki_page.id', $id]);
        }

        $output = [];
        foreach ($pages->each() as $page) {
            /* @var WikiPage $page */
            $output[] = [
                'id' => $page->id,
                'text' => $page->title,
            ];
        }

        return $this->asJson($output);
    }

    public function actionEntry(int $id = null)
    {
        if ($page = $this->getWikiPage($id)) {
            $revision = $this->getRevision($page);
            return $this->asJson([
                'output' => $this->renderAjax('_view_body', [
                    'page' => $page,
                    'revision' => $revision,
                    'canEdit' => $page->canEditContent(),
                    'content' => $revision->content,
                ]),
            ]);
        }

        return $this->asJson([
            'success' => false,
            'error' => 'No page found!',
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            [ControllerAccess::RULE_POST => ['sort', 'delete', 'revert']],
            [ControllerAccess::RULE_PERMISSION => [AdministerPages::class], 'actions' => ['sort', 'delete']],
            [ControllerAccess::RULE_PERMISSION => [CreatePage::class, EditPages::class, AdministerPages::class], 'actions' => ['edit']],
            [ControllerAccess::RULE_PERMISSION => [EditPages::class, AdministerPages::class], 'actions' => ['revert']],
            [ControllerAccess::RULE_PERMISSION => [ViewHistory::class], 'actions' => ['history']],
            [ControllerAccess::RULE_PERMISSION => [ViewHistory::class], 'actions' => ['history']],
        ];
    }


}
