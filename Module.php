<?php

namespace module\wiki;

use Yii;
use module\wiki\models\WikiPage;

class Module extends \humhub\components\Module
{

    public function behaviors()
    {
        return [
            //   \humhub\modules\user\behaviors\UserModule::className(),
            \humhub\modules\space\behaviors\SpaceModule::className(),
        ];
    }

    public function disable()
    {
        if (parent::disable()) {

            foreach (WikiPage::model()->findAll() as $page) {
                $page->delete();
            }

            return true;
        }

        return false;
    }

    public function getSpaceModuleDescription()
    {
        return Yii::t('WikiModule.base', 'Adds a wiki to this space.');
    }

    public function getUserModuleDescription()
    {
        return Yii::t('WikiModule.base', 'Adds a wiki to your profile.');
    }

    public function disableSpaceModule(Space $space)
    {
        /*
          foreach (WikiPage::model()->contentContainer($space)->findAll() as $page) {
          $page->delete();
          }
         */
    }

    public function disableUserModule(User $user)
    {
        /*
          foreach (WikiPage::model()->contentContainer($user)->findAll() as $page) {
          $page->delete();
          }
         * 
         */
    }

}
