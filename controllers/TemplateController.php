<?php

namespace humhub\modules\wiki\controllers;

use humhub\modules\wiki\models\WikiTemplate;
use humhub\modules\user\models\User;
use yii\web\NotFoundHttpException;
use humhub\libs\Helpers;
use humhub\libs\Html;
use Yii;


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
        $model = $this->findTemplate($id);

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
        $model = $this->findTemplate($id);
        $model->delete();

        return $this->redirect(['index', 'container' => $this->contentContainer]);
    }

    /**
     * Finds template and ensures it belongs to contentContainer
     */
    protected function findTemplate($id)
    {
        $model = WikiTemplate::find()->where(['contentcontainer_id' => $this->contentContainer->contentcontainer_id, 'id' => $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Template not found!');
        }
        return $model;
    }

    /**
     * API to get the content, placeholders from the template
     */
    public function actionGetTemplateContent($id)
    {
        $template = $this->findTemplate($id);
        $title = $template->title_template;
        $content = $template->content;
        $placeholders = $template->placeholders;

        if (!$template) {
            return $this->asJson(['success' => false]);
        }

        $username = Yii::$app->user->identity->username;

        $user = User::find()->where(['username' => $username])->one();

        return $this->asJson([
            'success' => true,
            'title' => $title,
            'content' => $content,
            'placeholders' => $placeholders,
            'is_appendable' => $template->is_appendable,
            'appendable_content' => $template->appendable_content,
            'appendable_content_placeholder' => $template->appendable_content_placeholder,
            'user' => ['guid' => $user->guid, 'displayName' => $user->displayName],
        ]);
    }
}
