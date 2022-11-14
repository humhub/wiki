<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\helpers;

use humhub\modules\ui\view\helpers\ThemeHelper;
use humhub\modules\wiki\models\WikiPage;
use Yii;

/**
 * Class Helper
 */
class Helper
{
    public static function isEnterpriseTheme(): bool
    {
        return array_key_exists('enterprise', ThemeHelper::getThemeTree(Yii::$app->view->theme));
    }

    public static function isCurrentPage(?WikiPage $page): bool
    {
        if (!$page) {
            return false;
        }

        $title = Yii::$app->request->get('title');
        if (!empty($title)) {
            $currentPage = WikiPage::findOne(['title' => $title]);
            return $currentPage && $currentPage->id == $page->id;
        }

        $id = Yii::$app->request->get('id', Yii::$app->request->get('categoryId'));
        if (!empty($id)) {
            $currentPage = WikiPage::findOne($id);
            return $currentPage && $currentPage->id == $page->id;
        }

        return false;
    }
}