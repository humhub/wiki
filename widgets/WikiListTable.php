<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\widgets\JsWidget;
use yii\data\ActiveDataProvider;

class WikiListTable extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'wiki.ListTable';

    /**
     * @inheritdoc
     */
    public $init = true;

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
            'options' => $this->getOptions(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return ['class' => 'table-responsive'];
    }
}
