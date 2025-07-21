<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\extensions\custom_pages\elements;

use humhub\modules\custom_pages\modules\template\elements\BaseContentRecordElementVariable;
use humhub\modules\custom_pages\modules\template\elements\BaseRecordElementVariable;
use humhub\modules\wiki\models\WikiPage;
use yii\db\ActiveRecord;

class WikiPageElementVariable extends BaseContentRecordElementVariable
{
    public string $title;
    public string $content;
    public string $isHome;
    public string $isAdminOnly;
    public int $sortOrder;
    public bool $isContainerMenu;
    public int $containerMenuOrder;
    public WikiPageElementVariable $parent;

    public function setRecord(?ActiveRecord $record): BaseRecordElementVariable
    {
        if ($record instanceof WikiPage) {
            $this->title = $record->title;
            $this->content = $record->latestRevision->content;
            $this->isHome = (bool) $record->is_home;
            $this->isAdminOnly = (bool) $record->admin_only;
            $this->sortOrder = (int) $record->sort_order;
            $this->isContainerMenu = (bool) $record->is_container_menu;
            $this->containerMenuOrder = (int) $record->container_menu_order;

            if ($record->parent_page_id !== null) {
                $this->parent = self::instance($this->elementContent)->setRecord($record->categoryPage);
            }
        }

        return parent::setRecord($record);
    }
}
