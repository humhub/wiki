<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\widgets;

use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\widgets\menu\MenuLink;
use Yii;

/**
 * The "Edit Page" link is used in wall entry context menu only when user
 * has no permission "Administer pages" but has permission "Edit pages"
 */
class EditPageLink extends MenuLink
{
    public WikiPage $record;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->record->isNewRecord // Exclude new Wiki Page
            || $this->record->content->canEdit() // User with permission "Administer pages" already can edit it completely
            || !$this->record->canEditContent() // Permission "Edit pages" is required for this "Edit Page" link
        ) {
            $this->setIsVisible(false);
            return;
        }

        $this->setLabel(Yii::t('ContentModule.base', 'Edit'));
        $this->setIcon('pencil');
        $this->setUrl(Url::toWikiEdit($this->record));
        $this->setHtmlOptions(['class' => 'stream-entry-edit-link']);
    }
}
