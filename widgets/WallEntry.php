<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

/**
 * @inheritdoc
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    /**
     * @inheritdoc
     */
    public $showFiles = false;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $revision = $this->contentObject->latestRevision;
        if ($revision === null) {
            return "";
        }
        
        return $this->render('wallEntry', array('wiki' => $this->contentObject, 'content' => $revision->content, 'justEdited' => $this->justEdited));
    }

}
