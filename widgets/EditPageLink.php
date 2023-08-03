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
    public function getLabel()
    {
        return Yii::t('WikiModule.base', 'Edit Page');
    }

    /**
     * @inheritdoc
     */
    public function getActionUrl()
    {
        return Url::toWikiEdit($this->record);
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
}
