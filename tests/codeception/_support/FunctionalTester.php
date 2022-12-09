<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki;

use humhub\modules\space\models\Space;
use humhub\modules\wiki\models\WikiPage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \FunctionalTester
{
    use _generated\FunctionalTesterActions;

    /**
     * @param $space
     * @param $title
     * @param $content
     * @param array $options
     * @return WikiPage
     */
    public function createWiki($space, $title, $content, $options = [])
    {
        if($space instanceof Space) {
            $space = $space->guid;
        }
        #
        $this->amOnSpace($space, '/wiki/page/edit');
        $this->fillField('WikiPage[title]', $title);
        $this->fillField('WikiPageRevision[content]', $content);

        if(isset($options['is_home']) && $options['is_home']) {
            $this->checkOption('WikiPage[is_home]');
        }

        if(isset($options['admin_only']) && $options['admin_only']) {
            $this->checkOption('#wikipage-admin_only');
        }

        if(isset($options['is_home']) && $options['is_home']) {
            $this->checkOption('#wikipage-is_home');
        }

        if(isset($options['isPublic']) && $options['isPublic']) {
            $this->checkOption('#pageeditform-ispublic');
        }

        if(isset($options['topics']) && $options['topics']) {
            $this->checkOption('PageEditForm[topics]');
        }

        $this->saveWiki();

        $this->see($title, '.wiki-page-content');
        $this->see($content, '#wiki-page-richtext');

        $page = WikiPage::findOne(['title' => $title]);

        if (!empty($options['category'])) {
            $page->parent_page_id = $options['category'];
            $page->save();
        }

        return $page;
    }

    public function saveWiki()
    {
        $this->click('Save', '#wiki-page-edit form');
    }

    public function seeInitPageWithCreateOption()
    {
        $this->see('Create a Wiki Page');
        $this->see('No pages created yet.');
        $this->see('So it\'s on you.');
        $this->see('Create the first page now.');
        $this->see('Let\'s go!');
    }

    public function seeInitPageWithoutCreateOption()
    {
        $this->see('Create a Wiki Page');
        $this->see('No pages created yet');
        $this->dontSee('So it\'s on you.');
        $this->dontSee('Create the first page now.');
        $this->dontSee('Let\'s go!');
    }

    public function seeInMenu($value)
    {
        $this->see($value, '.wiki-menu');
    }

    public function dontSeeInMenu($value)
    {
        $this->dontSee($value, '.wiki-menu');
    }

    public function seeCategory($value)
    {
        $this->see($value, '.page-title.page-is-category');
    }

    public function dontSeeCategory($value)
    {
        $this->dontSee($value, '.page-title.page-is-category');
    }

    public function seePageTitle($value)
    {
        $this->see($value, '.page-title');
    }

    public function dontPageTitle($value)
    {
        $this->dontSee($value, '.page-title');
    }


}
