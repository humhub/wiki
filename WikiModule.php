<?php

class WikiModule extends HWebModule
{

    public function init()
    {

        $this->setImport(array('wiki.components.*'));
        
        require(dirname(__FILE__) . '/vendors/cebe/markdown/inline/CodeTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/inline/EmphStrongTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/inline/LinkTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/inline/StrikeoutTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/inline/UrlLinkTrait.php');

        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/CodeTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/FencedCodeTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/HeadlineTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/HtmlTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/ListTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/QuoteTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/RuleTrait.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/block/TableTrait.php');

        require(dirname(__FILE__) . '/vendors/cebe/markdown/Parser.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/Markdown.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/MarkdownExtra.php');
        require(dirname(__FILE__) . '/vendors/cebe/markdown/GithubMarkdown.php');

        return parent::init();
    }

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
