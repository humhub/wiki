<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\wiki\tests\codeception\fixtures;

use humhub\modules\wiki\models\WikiPage;
use yii\test\ActiveFixture;

class WikiRevisionFixture extends ActiveFixture
{
    public $modelClass = WikiPage::class;
    public $dataFile = '@wiki/tests/codeception/fixtures/data/wikiRevision.php';

}
