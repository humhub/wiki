<?php


namespace wiki;


use humhub\modules\wiki\widgets\CategoryListItem;

class FunctionalPermissionTest
{

    public function _before()
    {
        CategoryListItem::clear();
    }
}