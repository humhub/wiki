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
            ['label' => 'Test1', 'value' => 'Test1', 'id' => 1],
            ['label' => 'Test2', 'value' => 'Test2', 'id' => 2],
            ['label' => 'Test3', 'value' => 'Test3', 'id' => 3],
            ['label' => 'Test4', 'value' => 'Test4', 'id' => 4],
        ]);
    }




}
