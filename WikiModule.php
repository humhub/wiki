<?php

class WikiModule extends HWebModule
{

    public function behaviors()
    {
        return array(
            'SpaceModuleBehavior' => array(
                'class' => 'application.modules_core.space.behaviors.SpaceModuleBehavior',
            ),
                /*
                  'UserModuleBehavior' => array(
                  'class' => 'application.modules_core.user.behaviors.UserModuleBehavior',
                  ),
                 */
        );
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
        foreach (WikiPage::model()->contentContainer($space)->findAll() as $page) {
            $page->delete();
        }
    }

    public function disableUserModule(User $user)
    {
        foreach (WikiPage::model()->contentContainer($user)->findAll() as $page) {
            $page->delete();
        }
    }

}
