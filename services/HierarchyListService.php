<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\services;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\wiki\controllers\PageController;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\HierarchyItem;
use humhub\modules\wiki\models\WikiPage;
use Yii;

class HierarchyListService
{
    private ContentContainerActiveRecord $container;

    /**
     * @var HierarchyItem[] $items
     */
    private array $items = [];

    private ?int $currentItemId;

    public function __construct(ContentContainerActiveRecord $container)
    {
        $this->container = $container;

        $query = WikiPage::find()
            ->select([
                WikiPage::tableName() . '.id',
                WikiPage::tableName() . '.parent_page_id',
                WikiPage::tableName() . '.title',
            ])
            ->contentContainer($this->container)
            ->readable()
            ->orderBy([
                WikiPage::tableName() . '.sort_order' => SORT_ASC,
                WikiPage::tableName() . '.title' => SORT_ASC,
            ])
            ->asArray();

        foreach ($query->each() as $wikiPage) {
            $this->items[$wikiPage['id']] = new HierarchyItem($wikiPage);
        }

        $userSettings = Yii::$app->user->isGuest ? null : Yii::$app->user->getIdentity()->getSettings();

        foreach ($this->items as $id => $item) {
            if ($item->parentId !== null &&
                isset($this->items[$item->parentId])) {
                $this->items[$item->parentId]->isCategory = true;
            }

            $this->items[$id]->isFolded = $userSettings
                ? $userSettings->get('wiki.foldedCategory.' . $id, false)
                : false;
        }

        $this->currentItemId = $this->getCurrentPageId();
    }

    /**
     * @param int|null $parentId
     * @return HierarchyItem[]
     */
    public function getItemsByParentId(?int $parentId): array
    {
        $items = [];
        foreach ($this->items as $item) {
            if ($item->parentId === $parentId) {
                $items[$item->id] = $item;
            }
        }

        return $items;
    }

    public function getItemChildrenCount(int $parentId): int
    {
        $subItems = $this->getItemsByParentId($parentId);
        if ($subItems === []) {
            return 0;
        }

        $childrenCount = 0;
        foreach ($subItems as $subItem) {
            $childrenCount += 1 + $this->getItemChildrenCount($subItem->id);
        }

        return $childrenCount;
    }

    public function isCurrentItem(?HierarchyItem $item): bool
    {
        return $item && $this->currentItemId === $item->id;
    }

    public function getNewWikiPageUrl(?HierarchyItem $item = null): string
    {
        return Url::wikiEdit($this->container, null, $item ? $item->id : null);
    }

    public function getWikiUrl(?HierarchyItem $item = null): ?string
    {
        return $item
            ? Url::to([Url::ROUTE_WIKI_PAGE, 'id' => $item->id, 'title' => $item->title, 'container' => $this->container])
            : null;
    }

    public function getItemVisibilityIconName(?HierarchyItem $item = null): ?string
    {
        if ($item === null) {
            return null;
        }

        if ($item->contentVisibility === $this->container->getDefaultContentVisibility()) {
            return null;
        }

        return $item->contentVisibility === Content::VISIBILITY_PUBLIC ? 'globe' : 'lock';
    }

    public function isFoldedItemById(?int $itemId): bool
    {
        return isset($this->items[$itemId]) ? $this->items[$itemId]->isFolded : false;
    }

    public function getCurrentPageId(): ?int
    {
        $controller = Yii::$app->controller;
        if (!($controller instanceof PageController) ||
            !$this->container->is($controller->contentContainer)) {
            return null;
        }

        $title = Yii::$app->request->get('title');
        if ($title !== null && $title !== '') {
            foreach ($this->items as $item) {
                if ($item->title === $title) {
                    return $item->id;
                }
            }
        }

        $id = (int) Yii::$app->request->get('id', Yii::$app->request->get('categoryId'));
        if ($id > 0 && isset($this->items[$id])) {
            return $id;
        }

        return null;
    }
}
