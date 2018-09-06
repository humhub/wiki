<?php

namespace humhub\modules\wiki\controllers;

use humhub\components\Controller;

/**
 * PageController
 *
 * @author luke
 */
class SearchController extends Controller
{

    public function actionSearch($term = null) {
        return $this->asJson([
            ['label' => 'Test1', 'value' => 1],
            ['label' => 'Test2', 'value' => 2],
            ['label' => 'Test3', 'value' => 3],
            ['label' => 'Test4', 'value' => 4],
        ]);
    }




}
