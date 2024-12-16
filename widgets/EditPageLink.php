<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\content\widgets\WallEntryControlLink;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use Yii;
use yii\helpers\Html;

/**
 * The "Edit Page" link is used in wall entry context menu only when user
 * has no permission "Administer pages" but has permission "Edit pages"
 */
class EditPageLink extends WallEntryControlLink
{
    public WikiPage $record;

    /**
     * @inheritdoc
     */
    public $icon = 'fa-pencil';

    /**
     * @inheritdoc
     */
    public $options = ['class' => 'stream-entry-edit-link'];

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return Yii::t('ContentModule.base', 'Edit');
    }

    /**
     * @inheritdoc
     */
    public function preventRender()
    {
        return $this->record->isNewRecord || // Exclude new Wiki Page
            $this->record->content->canEdit() || // User with permission "Administer pages" already can edit it completely
            !$this->record->canEditContent(); // Permission "Edit pages" is required for this "Edit Page" link
    }

    /**
     * @inheritdoc
     */
    protected function renderLink()
    {
        return Html::a($this->renderLinkText(), Url::toWikiEdit($this->record), $this->options);
    }
}
