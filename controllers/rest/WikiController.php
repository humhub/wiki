<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\controllers\rest;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\content\models\forms\MoveContentForm;
use humhub\modules\rest\components\BaseContentController;
use humhub\modules\wiki\helpers\RestDefinitions;
use humhub\modules\wiki\models\forms\PageEditForm;
use humhub\modules\wiki\models\forms\WikiPageItemDrop;
use humhub\modules\wiki\models\WikiPage;
use Yii;

class WikiController extends BaseContentController
{
    public static $moduleId = 'Wiki';

    /**
     * {@inheritdoc}
     */
    public function getContentActiveRecordClass()
    {
        return WikiPage::class;
    }

    /**
     * {@inheritdoc}
     */
    public function returnContentDefinition(ContentActiveRecord $contentRecord)
    {
        /** @var WikiPage $contentRecord */
        return RestDefinitions::getWikiPage($contentRecord);
    }

    public function actionCreate($containerId)
    {
        $containerRecord = ContentContainer::findOne(['id' => $containerId]);
        if ($containerRecord === null) {
            return $this->returnError(404, 'Content container not found!');
        }

        /** @var ContentContainerActiveRecord $container */
        $container = $containerRecord->getPolymorphicRelation();

        $pageParams = Yii::$app->request->post('WikiPage', []);
        $title = ! empty($pageParams['title']) ? $pageParams['title'] : null;
        $categoryId = ! empty($pageParams['parent_page_id']) ? $pageParams['parent_page_id'] : null;

        $wikiForm = (new PageEditForm(['container' => $container]))->forPage(null, $title, $categoryId);

        if ($wikiForm->load(Yii::$app->request->getBodyParams()) && $wikiForm->save()) {
            return RestDefinitions::getWikiPage($wikiForm->page);
        }

        if ($wikiForm->hasErrors() || $wikiForm->page->hasErrors()) {
            return $this->returnError(422, 'Validation failed', [
                'wikiForm' => $wikiForm->getErrors(),
                'page' => $wikiForm->page->getErrors(),
            ]);
        } else {
            Yii::error('Could not create validated wiki page.', 'api');
            return $this->returnError(500, 'Internal error while save valid wiki page!');
        }
    }

    public function actionUpdate($id)
    {
        $page = WikiPage::findOne(['id' => $id]);
        if (! $page) {
            return $this->returnError(404, 'Page not found!');
        }

        $wikiForm = (new PageEditForm(['container' => $page->content->container]))->forPage($page->id, $page->title, $page->parent_page_id);

        $bodyParams = Yii::$app->request->getBodyParams();

        if (!isset($bodyParams['PageEditForm']['latestRevisionNumber'])) {
            // Don't check latest revision on update from API by default
            $bodyParams['PageEditForm']['latestRevisionNumber'] = $wikiForm->latestRevisionNumber;
            $bodyParams['PageEditForm']['confirmOverwriting'] = 1;
        }

        if ($wikiForm->load($bodyParams) && $wikiForm->save()) {
            return RestDefinitions::getWikiPage(WikiPage::findOne(['id' => $wikiForm->page->id]));
        }

        if ($wikiForm->hasErrors() || $wikiForm->page->hasErrors()) {
            return $this->returnError(422, 'Validation failed', [
                'wikiForm' => $wikiForm->getErrors(),
                'page' => $wikiForm->page->getErrors(),
            ]);
        } else {
            Yii::error('Could not update validated wiki page.', 'api');
            return $this->returnError(500, 'Internal error while update wiki page!');
        }
    }

    public function actionChangeIndex($id)
    {
        $page = WikiPage::findOne(['id' => $id]);
        if (! $page) {
            return $this->returnError(404, 'Page not found!');
        }
        if (!$page->canEditWikiPage()) {
            return $this->returnError(403, 'You cannot edit the page!');
        }
        $requestParams = Yii::$app->request->getBodyParams();
        if (empty($requestParams['target_id'])) {
            return $this->returnError(400, 'Target category is required.');
        }
        $targetPage = WikiPage::findOne(['id' => $requestParams['target_id']]);
        if (! $targetPage || ! $targetPage->is_category) {
            return $this->returnError(400, 'Wrong target category.');
        }
        if (!$targetPage->canEditWikiPage()) {
            return $this->returnError(403, 'You cannot edit the target page!');
        }
        $formParams = [
            'id' => $id,
            'targetId' => $targetPage->id,
            'index' => isset($requestParams['index']) ? $requestParams['index'] : 0
        ];
        $moveModel = new WikiPageItemDrop(['contentContainer' => $page->content->container]);
        if($moveModel->load($formParams, '') && $moveModel->save()) {
            return $this->returnSuccess('Wiki page successfully moved!');
        } else {
            Yii::error('Could not move wiki page.', 'api');
            return $this->returnError(500, 'Internal error while change wiki page index!');
        }
    }

    public function actionMove($id)
    {
        $page = WikiPage::findOne(['id' => $id]);
        if (! $page) {
            return $this->returnError(404, 'Page not found!');
        }
        if (! $page->content) {
            return $this->returnError(404, 'Page content not found!');
        }
        if (!$page->canEditWikiPage()) {
            return $this->returnError(403, 'You cannot move the page!');
        }

        $target = Yii::$app->request->post('target', null);

        if (! $target) {
            return $this->returnError(400, 'Target content container guid is required!');
        }

        $moveForm = new MoveContentForm(['id' => $page->content->id]);
        $formData['MoveContentForm']['target'][] = $target;

        if($moveForm->load($formData) && $moveForm->save()) {
            return $this->returnSuccess('Wiki page successfully moved!');
        }

        if ($moveForm->hasErrors()) {
            return $this->returnError(422, 'Validation failed', [
                'errors' => $moveForm->getErrors()
            ]);
        } else {
            Yii::error('Could not move wiki page.', 'api');
            return $this->returnError(500, 'Internal error while move wiki page!');
        }
    }

}