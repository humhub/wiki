<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\components\Widget;
use yii\data\ActiveDataProvider;

class WikiListTable extends Widget
{
    /**
     * @var ActiveDataProvider
     */
    public $dataProvider;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('wikiListTable', [
            'dataProvider' => $this->dataProvider,
        ]);
    }
}