<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki;

use Yii;

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
class AcceptanceTester extends \AcceptanceTester
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    public function enableWikiOnProfile()
    {
        $this->amOnRoute(['/user/account/edit-modules']);
        $this->waitForText('Enable');
        $this->click('.enable-module-wiki');

        $buttonText = version_compare(Yii::$app->version, '1.16', '<') ? 'Activated' : 'Enabled';
        $this->waitForText($buttonText, null, '.disable-module-wiki');
    }

    /**
     * Create wiki entries
     *
     * @throws \Exception
     */
    public function createWikiEntries()
    {
        /**
         * CREATE CATEGORY
         */
        $this->amGoingTo('Create my first wiki category page');
        $this->waitForText('Create new page', 30);
        $this->fillField('#wikipage-title', 'First Test Wiki Category');
        $this->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My First Wiki Category!');
        $this->jsShow('.form-collapsible-fields.closed fieldset');
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->waitForElementVisible('.wiki-page-content');
        $this->waitForText('First Test Wiki Category');
        $this->see('My First Wiki Category!', 'h1');

        $this->toIndex();
        $this->see('First Test Wiki Category', '.wiki-page-list');

        /**
         * CREATE First SUB PAGE
         */
        $this->amGoingTo('Create my first sub page');
        $this->jsClick('[data-original-title="Add Page"]');
        $this->waitForText('Create new page', 30);

        $this->fillField('#wikipage-title', 'First Sub Page');
        $this->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My Sub Page!');
        $this->jsShow('.form-collapsible-fields.closed fieldset');
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->waitForElementVisible('.wiki-page-content');
        $this->waitForText('First Sub Page');
        $this->see('My Sub Page!', 'h1');

        $this->toIndex();
        $this->see('First Sub Page', '.wiki-page-list');

        /**
         * CREATE Second SUB PAGE
         */
        $this->amGoingTo('Create my second sub page');
        $this->click('Create page');
        $this->waitForText('Create new page', 30);

        $this->fillField('#wikipage-title', 'Second Page');
        $this->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My Second Page!');
        $this->jsShow('.form-collapsible-fields.closed fieldset');
        $this->selectFromPicker('#wikipage-parent_page_id', 'First Test Wiki Category');
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->waitForElementVisible('.wiki-page-content');
        $this->toIndex();
        $this->see('Second Page', '.wiki-page-list');

        /**
         * Set First As HomePage
         */
        $this->click('First Sub Page');

        $this->waitForText('My Sub Page', null, '.wiki-content');
        $this->click('Edit');
        $this->waitForElementVisible('#wiki-page-edit');
        $this->jsShow('.form-collapsible-fields.closed fieldset');
        $this->waitForElementVisible('[for="wikipage-is_home"]');
        $this->click('[for="wikipage-is_home"]');
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->waitForElementVisible('.wiki-menu');
        $this->jsClick('.wiki-menu .dropdown-toggle');
        $this->waitForText('Home', null, '.wiki-menu');
        $this->toIndex();

        $this->click('Wiki', '.layout-nav-container');

        $this->waitForText('My Sub Page', null, '.wiki-content');

        /**
         * Move Category
         * skipped due to travis search index issues
        $this->enableModule(3, 'wiki');
        $this->amOnSpace(1);
        //$this->wait(30);
        $this->waitForText('Wiki', null,'.layout-nav-container');
        $this->click('Wiki', '.layout-nav-container');


        $this->waitForElementVisible('#wiki_index');
        $this->toIndex();
        $this->click('First Test Wiki Category');
        $this->waitForText('My First Wiki Category');
        $this->click('Edit page');

        $this->waitForElementVisible('#wiki-page-edit');
        $this->click('Move content');

        $this->waitForText('Move content', null, '#globalModal');
        $this->selectUserFromPicker('#movecontentform-target', 'Space 3');

        $this->click('Save', '#globalModal');
        $this->waitForText('Index', null, '.wiki-content');
        $this->dontSee('First Test Wiki Category');
        $this->see('First Sub Page', '.wiki-page-list');
        $this->see('Second Page', '.wiki-page-list');
        $this->wait(1);
        $this->click('First Sub Page', '.wiki-page-list');
        $this->waitForText('My Sub Page');
         */

        $this->click('Edit');
        $this->waitForElementVisible('#wiki-page-edit');
        $this->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My Sub Page Updated!');
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->jsClick('.wiki-menu .dropdown-toggle');
        $this->waitForText('Page History');

        $this->see('My Sub Page Updated');

        $this->click('Page History');

        $this->waitForText('Page history', null, '.wiki-content');

        $this->click('show changes', '.wiki-page-history li:not(:first-child)');

        $this->waitForText('Edit', null, '.wiki-menu');
        $this->jsClick('.wiki-menu .dropdown-toggle');
        $this->waitForText('Revert this');
        $this->click('Revert this');

        $this->waitForText('Confirm page reverting', null, '#globalModalConfirm');
        $this->click('Revert', '#globalModalConfirm');

        $this->waitForText('My Sub Page!');
    }

    /**
     * Create wiki pages
     *
     * @param string $type
     * @throws \Exception
     */
    public function createWikiPages(string $type)
    {
        /**
         * CREATE PUBLIC PAGE
         */
        $this->amGoingTo("Create my public {$type} wiki page");
        $this->waitForText('Create new page', 30);
        $this->fillField('#wikipage-title', "First Public {$type} Wiki Page");
        $this->fillField('#wikipagerevision-content .humhub-ui-richtext', "# My First Wiki {$type} Public Page!");
        $this->jsShow('.form-collapsible-fields.closed fieldset');
        $this->jsClick('#pageeditform-ispublic');
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->waitForElementVisible('.wiki-page-content');
        $this->see("First Public {$type} Wiki Page");
        $this->wait(1);
        $this->see("My First Wiki {$type} Public Page!", 'h1');

        $this->seeElement('.fa-globe');
        $this->toIndex();
        $this->see("First Public {$type} Wiki Page", '.wiki-page-list');

        /**
         * CREATE PRIVATE PAGE
         */
        $this->amGoingTo("Create my private {$type} wiki page");
        $this->click('Create page');
        $this->waitForText('Create new page', 30);
        $this->fillField('#wikipage-title', "First Private {$type} Wiki Page");
        $this->fillField('#wikipagerevision-content .humhub-ui-richtext', "# My First Wiki {$type} Private Page!");
        $this->click('Save', '#wiki-page-edit form');
        $this->seeSuccess();

        $this->waitForElementVisible('.wiki-page-content');
        $this->see("First Private {$type} Wiki Page");
        $this->wait(1);
        $this->see("My First Wiki {$type} Private Page!", 'h1');
        $this->dontSee("Public", 'h1 .label-info');
        $this->toIndex();
        $this->see("First Public {$type} Wiki Page", '.wiki-page-list');
        $this->see("First Private {$type} Wiki Page", '.wiki-page-list');
    }

    /**
     * Go to index of wiki pages
     *
     * @throws \Exception
     */
    public function toIndex()
    {
        $this->waitForElementVisible('#wiki_index');
        $this->click('#wiki_index');
        $this->waitForText('Wiki', null, '.wiki-page-content-header h3');
    }

    /**
     * Show a wiki page in Container menu
     *
     * @param string $wikiPageTitle
     * @param string $sidebarSelector
     * @throws \Exception
     */
    public function showWikiPageInContainerMenu(string $wikiPageTitle, string $sidebarSelector)
    {
        $this->click('Wiki', '.layout-nav-container');
        $this->waitForText('Wiki', null, '.wiki-page-content .wiki-page-content-header');
        $this->dontSee($wikiPageTitle, $sidebarSelector);

        $this->waitForText($wikiPageTitle, null, '.wiki-page-content');
        $this->click($wikiPageTitle, '.wiki-page-content');
        $this->waitForText('Edit', null, '.wiki-menu');
        $this->click('Edit', '.wiki-menu');
        $this->waitForText('Advanced settings');
        $this->jsShow('.form-collapsible-fields.closed fieldset');
        $this->wait(1);
        $this->click('[for="wikipage-is_container_menu"]');
        $this->fillField('#wikipage-container_menu_order', 100);
        $this->click('Save', '#wiki-page-edit form');

        $this->waitForText($wikiPageTitle, 10, $sidebarSelector);
    }

    // Create Category
    // Add Page
    // Move Page
    // Set as homepage
    // Edit and revert
}
