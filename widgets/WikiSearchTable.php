<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\search\ResultSet;
use humhub\widgets\JsWidget;

class WikiSearchTable extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'wiki.ListTable';

    /**
     * @inheritdoc
     */
    public $init = true;

    public ?ResultSet $resultSet = null;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('wikiSearchTable', [
            'options' => $this->getOptions(),
            'resultSet' => $this->resultSet,
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
