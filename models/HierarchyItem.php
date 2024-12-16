<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\models;

class HierarchyItem
{
    public int $id = 0;
    public ?int $parentId = null;
    public string $title = '';
    public ?int $contentVisibility = null;

    public bool $isCategory = false;
    public bool $isFolded = false;

    public function __construct(array $wikiPage)
    {
        $this->id = $wikiPage['id'];
        $this->parentId = $wikiPage['parent_page_id'];
        $this->title = $wikiPage['title'];
        $this->contentVisibility = $wikiPage['content']['visibility'] ?? null;
    }
}
