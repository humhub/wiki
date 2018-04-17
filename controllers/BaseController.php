<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\wiki\models\WikiPage;


/**
 * Class BaseController
 * @package humhub\modules\wiki\controllers
 */
abstract class BaseController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
    public $hideSidebar = true;

    /**
     * @return boolean can create new wiki site
     * @throws \yii\base\InvalidConfigException
     */
    public function canCreatePage()
    {
        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\CreatePage());
    }

    /**
     * @return boolean can view wiki page history?
     * @throws \yii\base\InvalidConfigException
     */
    public function canViewHistory()
    {
        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\ViewHistory());
    }

    /**
     * @return WikiPage the homepage
     * @throws \yii\base\Exception
     */
    protected function getHomePage()
    {
        return WikiPage::find()->contentContainer($this->contentContainer)->readable()->where(['is_home' => 1])->one();
    }

    /**
     * @return boolean can manage wiki sites?
     * @throws \yii\base\InvalidConfigException
     */
    public function canAdminister()
    {
        return $this->contentContainer->permissionManager->can(new \humhub\modules\wiki\permissions\AdministerPages());
    }


    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    protected function hasPages()
    {
        return (WikiPage::find()->contentContainer($this->contentContainer)->count() > 0);
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    protected function hasCategoryPages()
    {
        return (WikiPage::find()->contentContainer($this->contentContainer)->andWhere(['is_category' => 1])->count() > 0);
    }

}