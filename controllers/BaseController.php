<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\wiki\helpers\Helper;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\ViewHistory;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class BaseController
 * @package humhub\modules\wiki\controllers
 */
abstract class BaseController extends ContentContainerController
{
    /**
     * @return bool can create new wiki site
     * @throws \yii\base\InvalidConfigException
     */
    public function canCreatePage()
    {
        return (new WikiPage($this->contentContainer))->content->canEdit();
    }

    /**
     * @return bool can view wiki page history?
     * @throws \yii\base\InvalidConfigException
     */
    public function canViewHistory()
    {
        return $this->contentContainer->permissionManager->can(ViewHistory::class);
    }

    /**
     * @return WikiPage the homepage
     * @throws \yii\base\Exception
     */
    protected function getHomePage()
    {
        return WikiPage::getHome($this->contentContainer);
    }

    /**
     * @return bool can manage wiki sites?
     * @throws \yii\base\InvalidConfigException
     */
    public function canAdminister()
    {
        return $this->contentContainer->permissionManager->can(AdministerPages::class);
    }


    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    protected function hasPages(): bool
    {
        return WikiPage::find()->contentContainer($this->contentContainer)->exists();
    }

    /**
     * Render a content with sidebar when current theme is "Enterprise" or produced from it
     *
     * @param array|string $views 0 - View without sidebar, 1 - View with sidebar(if not set then use view from 0 key)
     * @param array $params
     * @return string
     * @throws NotFoundHttpException|\yii\base\InvalidConfigException
     */
    protected function renderSidebarContent($views, array $params = []): string
    {
        if (is_string($views)) {
            $normalView = $sidebarView = $views;
        } elseif (is_array($views) && isset($views[0])) {
            $normalView = $views[0];
            $sidebarView = $views[1] ?? $normalView;
        } else {
            throw new NotFoundHttpException();
        }

        if (Helper::isEnterpriseTheme()) {
            return $this->render('@wiki/views/common/sidebar-content', [
                'contentContainer' => $this->contentContainer,
                'canCreate' => $this->canCreatePage(),
                'hideSidebarOnSmallScreen' => $params['hideSidebarOnSmallScreen'] ?? true,
                'content' => $this->renderPartial($sidebarView, $params),
            ]);
        }

        return $this->render($normalView, $params);
    }

    public function updateFoldingState(int $wikiPageId, int $state)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        if (empty($wikiPageId)) {
            return;
        }

        $userSettings = Yii::$app->user->getIdentity()->getSettings();
        $foldingStateParamName = 'wiki.foldedCategory.' . $wikiPageId;

        if ($state) {
            $userSettings->set($foldingStateParamName, true);
        } else {
            $userSettings->delete($foldingStateParamName);
        }
    }
}
