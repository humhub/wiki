<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by FunkycraM (marc.fun)
 * Date: 18.12.2019
 * Time: 13:00
 */

namespace humhub\modules\wiki\controllers;


use Yii;
use humhub\modules\admin\permissions\ManageSpaces;
use humhub\modules\calendar\permissions\ManageEntry;
use humhub\modules\content\components\ContentContainerController;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\HttpException;
use humhub\modules\wiki\models\DefaultSettings;

class ContainerConfigController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getAccessRules()
    {
        return [
          ['permission' => [ManageSpaces::class, ManageEntry::class]]
        ];
    }

    public function actionIndex()
    {
        $model = new DefaultSettings(['contentContainer' => $this->contentContainer]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('@wiki/views/common/defaultConfig', [
            'model' => $model
        ]);
    }
}
