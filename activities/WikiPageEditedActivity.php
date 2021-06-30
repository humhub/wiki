<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\activities;

use humhub\modules\activity\components\BaseActivity;

/**
 * WikiPageEdited activity
 *
 * @author luke
 */
class WikiPageEditedActivity extends BaseActivity
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'wiki';

    /**
     * @inheritdoc
     */
    public $viewName = 'wikiPageEdited';

}
