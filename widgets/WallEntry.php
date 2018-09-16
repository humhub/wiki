<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\wiki\helpers\Url;

/**
 * @inheritdoc
 */
class WallEntry extends \humhub\modules\content\widgets\WallEntry
{

    /**
     * @inheritdoc
     */
    public $showFiles = false;

    public $editMode = self::EDIT_MODE_NEW_WINDOW;

    public function getEditUrl()
    {
        return Url::toWikiEdit($this->contentObject);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $revision = $this->contentObject->latestRevision;
        if ($revision === null) {
            return "";
        }
        
        return $this->render('wallEntry', ['wiki' => $this->contentObject, 'content' => $revision->content, 'justEdited' => $this->justEdited]);
    }

}
