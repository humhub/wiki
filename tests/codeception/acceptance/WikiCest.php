<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\acceptance;

use wiki\AcceptanceTester;

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

        $I->createWikiEntries();
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testInstallProfileEntry(AcceptanceTester $I)
    {
        $I->amUser1();

        $I->enableWikiOnProfile();

        $I->amOnUser1Profile();

        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $I->createWikiEntries();
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

        $I->enableWikiOnProfile();

        $I->amOnUser1Profile();
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Profile');

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

        $I->createWikiPages('Space');

        $I->logout();

        $I->amOnSpace(2);
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Index', null, '.wiki-content');
        $I->see('First Public Space Wiki Page', '.wiki-page-list');
        $I->dontSee('First Private Space Wiki Page', '.wiki-page-list');
    }

    public function testWikiPageInSpaceMenu(AcceptanceTester $I)
    {
        $I->amAdmin();

        $I->amUser1(true);
        $I->enableModule(2, 'wiki');
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Space');

        $I->showWikiPageInContainerMenu('First Public Space Wiki Page', '#space-main-menu');
        $I->showWikiPageInContainerMenu('First Private Space Wiki Page', '#space-main-menu');
    }

    public function testWikiPageInProfileMenu(AcceptanceTester $I)
    {
        $I->amUser1();

        $I->enableWikiOnProfile();

        $I->amOnUser1Profile();

        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Profile');

        $I->showWikiPageInContainerMenu('First Public Profile Wiki Page', '.profile-content .left-navigation');
        $I->showWikiPageInContainerMenu('First Private Profile Wiki Page', '.profile-content .left-navigation');
    }
}