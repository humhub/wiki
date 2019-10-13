<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\acceptance;

use humhub\modules\wiki\helpers\Url;
use PHPUnit_Framework_Test;
use wiki\AcceptanceTester;
use Yii;

class WikiCest
{

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testInstallSpaceEntry(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(1, 'wiki');
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $this->createWikiEntries($I);
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testInstallProfileEntry(AcceptanceTester $I)
    {
        $I->amUser1();

        $this->enableWikiOnProfile($I);

        $I->amOnUser1Profile();

        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $this->createWikiEntries($I);
    }

    private function enableWikiOnProfile(AcceptanceTester $I)
    {
        $I->amOnRoute(['/user/account/edit-modules']);
        $I->waitForText('Enable');

        if(version_compare( Yii::$app->version  ,'1.4-dev', '<')) {
            // Note: this only works if no other profile module is installed
            $I->click('Enable');
        } else {
            $I->click('.enable-module-wiki');
        }

        $I->waitForText('Disable');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testGuestAccessToProfileWiki(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->allowGuestAccess();

        $I->amUser1(true);
        $I->amOnRoute(['/user/account/edit-settings']);
        $I->waitForElement('#accountsettings-visibility');
        $I->selectOption('#accountsettings-visibility', 2);
        $I->seeOptionIsSelected('#accountsettings-visibility', 'Visible for all (also unregistered users)');
        $I->click('Save');
        $I->wait(1);

        $this->enableWikiOnProfile($I);

        $I->amOnUser1Profile();
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $this->createWikiPages($I, 'Profile');

        $I->logout();

        $I->amOnUser1Profile();
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Index', null, '.wiki-content');
        $I->see('First Public Profile Wiki Page', '.wiki-page-list');
        $I->dontSee('First Private Profile Wiki Page', '.wiki-page-list');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testGuestAccessToSpaceWiki(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->allowGuestAccess();

        $I->amUser1(true);
        $I->enableModule(2, 'wiki');
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $this->createWikiPages($I, 'Space');

        $I->logout();

        $I->amOnSpace(2);
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Index', null, '.wiki-content');
        $I->see('First Public Space Wiki Page', '.wiki-page-list');
        $I->dontSee('First Private Space Wiki Page', '.wiki-page-list');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    private function createWikiEntries($I)
    {
        /**
         * CREATE CATEGORY
         */
        $I->amGoingTo('Create my first wiki category page');
        $I->waitForText('Create new page', 30);
        $I->fillField('#wikipage-title', 'First Test Wiki Category');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My First Wiki Category!');
        $I->click('[for="wikipage-is_category"]');
        $I->click('Save');

        $I->waitForElementVisible('.wiki-page-content');
        $I->see('First Test Wiki Category');
        $I->wait(1);
        $I->see('My First Wiki Category!', 'h1');

        $this->toIndex($I);
        $I->see('First Test Wiki Category', '.wiki-page-list');


        /**
         * CREATE First SUB PAGE
         */
        $I->amGoingTo('Create my first sub page');
        $I->jsClick('[data-original-title="Add Page"]');
        $I->waitForText('Create new page', 30);

        $I->fillField('#wikipage-title', 'First Sub Page');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My Sub Page!');
        $I->seeOptionIsSelected('#wikipage-parent_page_id', 'First Test Wiki Category');
        $I->click('Save');

        $I->waitForElementVisible('.wiki-page-content');
        $I->see('First Sub Page');
        $I->see('My Sub Page!', 'h1');

        $this->toIndex($I);
        $I->see('First Sub Page', '.wiki-page-list');


        /**
         * CREATE Second SUB PAGE
         */

        $I->amGoingTo('Create my second sub page');
        $I->jsClick('[data-original-title="Add Page"]');
        $I->waitForText('Create new page', 30);

        $I->fillField('#wikipage-title', 'Second Page');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My Second Page!');
        $I->seeOptionIsSelected('#wikipage-parent_page_id', 'First Test Wiki Category');
        $I->click('Save');

        $I->waitForElementVisible('.wiki-page-content');
        $this->toIndex($I);
        $I->see('Second Page', '.wiki-page-list');

        /**
         * Set First As HomePage
         */
        $I->click('First Sub Page');

        $I->waitForText('My Sub Page', null,'.wiki-content');
        $I->click('Edit page');
        $I->waitForElementVisible('#wiki-page-edit');
        $I->click('[for="wikipage-is_home"]');
        $I->click('Save');

        $I->waitForText('Home', null, '.wiki-menu');
        $this->toIndex($I);

        $I->click('Wiki', '.layout-nav-container');

        $I->waitForText('My Sub Page', null,'.wiki-content');

        /**
         * Move Category
         * skipped due to travis search index issues
        $I->enableModule(3, 'wiki');
        $I->amOnSpace(1);
        //$I->wait(30);
        $I->waitForText('Wiki', null,'.layout-nav-container');
        $I->click('Wiki', '.layout-nav-container');


        $I->waitForElementVisible('#wiki_index');
        $this->toIndex($I);
        $I->click('First Test Wiki Category');
        $I->waitForText('My First Wiki Category');
        $I->click('Edit page');

        $I->waitForElementVisible('#wiki-page-edit');
        $I->click('Move content');

        $I->waitForText('Move content', null, '#globalModal');
        $I->selectUserFromPicker('#movecontentform-target', 'Space 3');

        $I->click('Save', '#globalModal');
        $I->waitForText('Index', null, '.wiki-content');
        $I->dontSee('First Test Wiki Category');
        $I->see('First Sub Page', '.wiki-page-list');
        $I->see('Second Page', '.wiki-page-list');
        $I->wait(1);
        $I->click('First Sub Page', '.wiki-page-list');
        $I->waitForText('My Sub Page');
        */


        $I->click('Edit page');
        $I->waitForElementVisible('#wiki-page-edit');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My Sub Page Updated!');
        $I->click('Save');

        $I->waitForText('Page History');

        $I->see('My Sub Page Updated');

        $I->click('Page History');

        $I->waitForText('Page history', null, '.wiki-content');

        $I->click('View', '.wiki-page-history .media:not(.alert)');

        $I->waitForText('Revert this');
        $I->click('Revert this');

        $I->waitForText('Confirm page reverting', null, '#globalModalConfirm');
        $I->click('Revert', '#globalModalConfirm');

        $I->waitForText('My Sub Page!');
    }

    /**
     * @param AcceptanceTester $I
     * @param $type
     * @throws \Exception
     */
    private function createWikiPages($I, $type)
    {
        /**
         * CREATE PUBLIC PAGE
         */
        $I->amGoingTo("Create my public {$type} wiki page");
        $I->waitForText('Create new page', 30);
        $I->fillField('#wikipage-title', "First Public {$type} Wiki Page");
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', "# My First Wiki {$type} Public Page!");
        $I->click('[for="pageeditform-ispublic"]');
        $I->click('Save');

        $I->waitForElementVisible('.wiki-page-content');
        $I->see("First Public {$type} Wiki Page");
        $I->wait(1);
        $I->see("My First Wiki {$type} Public Page!", 'h1');

        $I->seeElement('.fa-globe');
        $this->toIndex($I);
        $I->see("First Public {$type} Wiki Page", '.wiki-page-list');

        /**
         * CREATE PRIVATE PAGE
         */
        $I->amGoingTo('Create my private wiki page');
        $I->click('New page');
        $I->waitForText('Create new page', 30);
        $I->fillField('#wikipage-title', "First Private {$type} Wiki Page");
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', "# My First Wiki {$type} Private Page!");
        $I->click('Save');

        $I->waitForElementVisible('.wiki-page-content');
        $I->see("First Private {$type} Wiki Page");
        $I->wait(1);
        $I->see("My First Wiki {$type} Private Page!", 'h1');
        $I->dontSee("Public", 'h1 .label-info');
        $this->toIndex($I);
        $I->see("First Public {$type} Wiki Page", '.wiki-page-list');
        $I->see("First Private {$type} Wiki Page", '.wiki-page-list');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    private function toIndex($I)
    {
        $I->click('#wiki_index');
        $I->waitForText('Index', null, '.wiki-content');
    }

    // Create Category
    // Add Page
    // Move Page
    // Set as homepage
    // Edit and revert
}