<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\helpers;

use humhub\modules\ui\view\helpers\ThemeHelper;
use humhub\modules\wiki\controllers\PageController;
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

    public static function getCurrentPageId(): ?int
    {
        return Yii::$app->runtimeCache->getOrSet('current-wiki-page', function () {
            $controller = Yii::$app->controller;
            if (!$controller instanceof PageController) {
                return null;
            }

            $title = Yii::$app->request->get('title');
            if ($title !== null && $title !== '') {
                return WikiPage::find()
                    ->select(WikiPage::tableName() . 'id')
                    ->where([WikiPage::tableName() . '.title' => $title])
                    ->contentContainer($controller->contentContainer)
                    ->scalar();
            }

            $id = Yii::$app->request->get('id', Yii::$app->request->get('categoryId'));
            if (!empty($id)) {
                return WikiPage::find()
                    ->where([WikiPage::tableName() . '.id' => $id])
                    ->contentContainer($controller->contentContainer)
                    ->exists() ? $id : null;
            }

            return null;
        });
    }

    public static function isCurrentPage(?WikiPage $page): bool
    {
        return $page && self::getCurrentPageId() === $page->id;
    }

    public static function isFolderPageById(?int $pageId): bool
    {
        return $pageId &&
            !Yii::$app->user->isGuest &&
            Yii::$app->user->getIdentity()->getSettings()->get('wiki.foldedCategory.' . $pageId);
    }
}
