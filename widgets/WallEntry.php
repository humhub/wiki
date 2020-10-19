<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\wiki\helpers\Url;

/**
 * @inheritdoc
 */
class WallEntry extends WallStreamModuleEntryWidget
{

    /**
     * @inheritdoc
     */
    public $showFiles = false;

    public $editMode = self::EDIT_MODE_NEW_WINDOW;

    public function getEditUrl()
    {
        return Url::toWikiEdit($this->model);
    }

    /**
     * @inheritdoc
     */
    public function renderContent()
    {
        $revision = $this->model->latestRevision;
        if ($revision === null) {
            return '';
        }

        return $this->render('wallEntry', ['wiki' => $this->model, 'content' => $revision->content, 'justEdited' => $this->renderOptions->isJustEdited()]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }
}
