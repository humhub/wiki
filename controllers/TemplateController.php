<?php

namespace humhub\modules\wiki\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\wiki\models\WikiTemplate;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextConverter;
use humhub\libs\Helpers;

class TemplateController extends BaseController
{

    /**
     * Lists templates in current content container (space)
     */
    public function actionIndex()
    {
        $templates = WikiTemplate::find()
        ->where(['contentcontainer_id' => $this->contentContainer->contentcontainer_id])
        ->all();

        return $this->render('index', [
            'templates' => $templates,
            'container' => $this->contentContainer
        ]);
    }

    /**
     * Creates new Template in current content container
     */
    public function actionCreate()
    {
        $model = new WikiTemplate();
        $model->contentcontainer_id = $this->contentContainer->contentcontainer_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'container' => $this->contentContainer]);
        }

        return $this->render('edit', ['model' => $model, 'container' => $this->contentContainer]);
    }

    /**
     * Updates existing template
     */
    public function actionEdit($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'container' => $this->contentContainer]);
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * Deletes template
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index', 'container' => $this->contentContainer]);
    }

    /**
     * Finds template and ensures it belongs to contentContainer
     */
    protected function findModel($id)
    {
        $model = WikiTemplate::find()->where(['contentcontainer_id' => $this->contentContainer->contentcontainer_id, 'id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Template not found!');
        }
        return $model;
    }


    public function actionGetTemplateContent($id)
    {
        $template = $this->findModel($id);
        $title = $template->title_template;
        $content = $template->content;
        $placeholders = $template->placeholders;

        if (!$template) {
            return $this->asJson(['success' => false]);
        }

        $converter = new ProsemirrorRichTextConverter();

        $content = $converter->convertToHtml($content);

        return $this->asJson(['success' => true, 'title' => $title, 'content' => $content, 'placeholders'=> $placeholders]);
    }
}
