<?php

class WikiModule extends HWebModule
{

    public function init()
    {
        require(dirname(__FILE__) . '/vendors/markdown/inline/CodeTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/inline/EmphStrongTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/inline/LinkTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/inline/StrikeoutTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/inline/UrlLinkTrait.php');

        require(dirname(__FILE__) . '/vendors/markdown/block/CodeTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/FencedCodeTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/HeadlineTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/HtmlTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/ListTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/QuoteTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/RuleTrait.php');
        require(dirname(__FILE__) . '/vendors/markdown/block/TableTrait.php');
        
        require(dirname(__FILE__) . '/vendors/markdown/Parser.php');
        require(dirname(__FILE__) . '/vendors/markdown/Markdown.php');
        require(dirname(__FILE__) . '/vendors/markdown/MarkdownExtra.php');
        require(dirname(__FILE__) . '/vendors/markdown/GithubMarkdown.php');



        return parent::init();
    }

    public function behaviors()
    {
        return array(
            'SpaceModuleBehavior' => array(
                'class' => 'application.modules_core.space.behaviors.SpaceModuleBehavior',
            ),
            'UserModuleBehavior' => array(
                'class' => 'application.modules_core.user.behaviors.UserModuleBehavior',
            ),
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
