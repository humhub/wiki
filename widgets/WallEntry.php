<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\libs\Helpers;
use humhub\modules\content\widgets\richtext\converter\BaseRichTextConverter;
use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;

/**
 * @inheritdoc
 */
class WallEntry extends WallStreamModuleEntryWidget
{
    /**
     * @inheritdoc
     */
    public $createRoute = '/wiki/page/edit';

    /**
     * @inheritdoc
     */
    public $showFiles = false;

    /**
     * @inheritdoc
     */
    public $editMode = self::EDIT_MODE_NEW_WINDOW;

    /**
     * @var bool
     */
    public $disabledWallEntryControls = false;

    /**
     * @inheritdoc
     * @var WikiPage $model
     */
    public $model;

    /**
     * @inerhitdoc
     */
    public function init()
    {
        parent::init();

        if ($this->disabledWallEntryControls) {
            if ($this->renderOptions && $this->model->isNewRecord) {
                $this->renderOptions->disableControlsMenu();
                $this->renderOptions->disableControlsEntryTopics();
            }
            $this->renderOptions->disableControlsEntryEdit();
            $this->renderOptions->disableControlsEntryPermalink();
            $this->renderOptions->disableControlsEntryDelete();
            $this->renderOptions->disableControlsEntryPin();
            $this->renderOptions->disableControlsEntryMove();
            $this->renderOptions->disableControlsEntryArchive();
        }
    }

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

        $content = $revision->content;
        if (!empty($content)) {
            $content = Helpers::truncateText($content, 500);
            // Cleanup a broken link or image in the end after truncation
            $content = preg_replace('/!?\[.+?\]\([^\)]*?\.\.\.$/', '...', $content);
            $content = WikiRichText::output($content, [BaseRichTextConverter::OPTION_EXCLUDE => ['anchor']]);
        }

        return $this->render('wallEntry', [
            'wiki' => $this->model,
            'content' => $content,
        ]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }
}
