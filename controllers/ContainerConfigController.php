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

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\wiki\models\DefaultSettings;
use Yii;

class ContainerConfigController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [[ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Space::USERGROUP_ADMIN, User::USERGROUP_SELF]]];
    }

    public function actionIndex()
    {
        $model = new DefaultSettings(['contentContainer' => $this->contentContainer]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->saved();
        }

        return $this->render('@wiki/views/common/defaultConfig', [
            'model' => $model,
        ]);
    }
}
