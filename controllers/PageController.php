<?php

namespace humhub\modules\wiki\controllers;

use Yii;
use yii\base\Exception;
use yii\web\HttpException;
use humhub\components\access\ControllerAccess;
use humhub\modules\wiki\helpers\HeadlineExtractor;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\forms\PageEditForm;
use humhub\modules\wiki\models\forms\WikiPageItemDrop;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\wiki\permissions\ViewHistory;

/**
 * PageController
 *
 * @author luke
 */
class PageController extends BaseController
{
    public function getAccessRules()
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

    /**
     * @return $this|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        return $this->redirect($this->contentContainer->createUrl('/wiki/overview'));
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionList()
    {
        return $this->redirect($this->contentContainer->createUrl('/wiki/overview/list-categories'));
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function actionView($title = null, $revisionId = null)
    {
        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['title' => $title])->one();

        if(!$page && $this->canCreatePage()) {
            return $this->redirect(Url::toWikiCreateByTitle($this->contentContainer, $title));
        }

        if(!$page) {
            throw new HttpException(404, 'Wiki page not found!');
        }

        $revision = $this->getRevision($page, $revisionId);

        // There is no revision for this page.
        if (!$revision && $this->canCreatePage()) {
            $page->delete();
            return $this->redirect(Url::toWikiCreateByTitle($this->contentContainer, $title));
        }

        if(!$revision) {
            $page->delete();
            throw new HttpException(404, 'Wiki page revision not found!');
        }

        return $this->render('view', [
            'page' => $page,
            'revision' => $revision,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'content' => $revision->content,
            'canViewHistory' => $this->canViewHistory(),
            'canEdit' => $this->canEdit($page),
            'canAdminister' => $this->canAdminister(),
            'canCreatePage' => $this->canCreatePage()
        ]);
    }

    /**
     * Returns a revision for the given page, either by a given revisionid or the latest.
     *
     * @param $page
     * @param $revisionId
     * @return WikiPageRevision|null
     */
    private function getRevision($page, $revisionId = null)
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
     * @return $this|string|\yii\web\Response
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionEdit($id = null, $title = null, $categoryId = null)
    {
        $form = (new PageEditForm(['container' => $this->contentContainer]))->forPage($id,$title,$categoryId);

        if($form->load(Yii::$app->request->post()) && $form->save()) {
            return $this->redirect(Url::toWiki($form->page));
        }

        return $this->render('edit', [
            'model' => $form,
            'homePage' => $this->getHomePage(),
            'contentContainer' => $this->contentContainer,
            'canAdminister' => $this->canAdminister()
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     * @throws HttpException
     */
    public function actionHeadlines($id) {
        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();

        if(!$page) {
            throw new HttpException(404);
        }

        return $this->asJson(HeadlineExtractor::extract($page->latestRevision->content));
    }

    public function actionSort()
    {
        $dropModel = new WikiPageItemDrop(['contentContainer' => $this->contentContainer]);
        if($dropModel->load(Yii::$app->request->post()) && $dropModel->save()) {
            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['success' => false]);
    }

    /**
     * @return string
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
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

        $pagination = new \yii\data\Pagination(['totalCount' => $countQuery->count(), 'pageSize' => "20"]);
        $query->offset($pagination->offset)->limit($pagination->limit);


        return $this->render('history', [
                'page' => $page,
                'revisions' => $query->all(),
                'pagination' => $pagination,
                'homePage' => $this->getHomePage(),
                'contentContainer' => $this->contentContainer]
        );
    }

    /**
     * @return $this|\yii\web\Response
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $page = WikiPage::find()->contentContainer($this->contentContainer)->where(['wiki_page.id' => $id])->one();

        if (!$page) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        $page->delete();

        return $this->redirect($this->contentContainer->createUrl('index'));
    }

    /**
     * @param int $id
     * @param int $toRevision
     * @return $this|\yii\web\Response
     * @throws Exception
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRevert($id, $toRevision)
    {
        $page = WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['wiki_page.id' => $id])->one();

        if (!$page) {
            throw new HttpException(404, Yii::t('WikiModule.base', 'Page not found.'));
        }

        if (!$page->content->canEdit()) {
            throw new HttpException(403, Yii::t('WikiModule.base', 'Page not editable!'));
        }

        $revision = WikiPageRevision::findOne([
            'revision' => $toRevision,
            'wiki_page_id' => $page->id
        ]);

        if(!$revision) {
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
            'redirect' => Url::toWiki($page)
        ]);
    }

    /**
     * @param WikiPage $page
     * @return boolean can edit given wiki site?
     * @throws \yii\base\InvalidConfigException
     */
    public function canEdit($page)
    {
        return $page->content->canEdit();
    }
}
