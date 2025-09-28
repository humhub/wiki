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
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
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
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
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
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Profile');

        $I->logout();

        $I->amOnUser1Profile();
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Wiki', 15, '.wiki-content');
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
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Space');

        $I->logout();

        $I->amOnSpace(2);
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Wiki', 15, '.wiki-content');
        $I->see('First Public Space Wiki Page', '.wiki-page-list');
        $I->dontSee('First Private Space Wiki Page', '.wiki-page-list');
    }

    public function testWikiPageInSpaceMenu(AcceptanceTester $I)
    {
        $I->amAdmin();

        $I->amUser1(true);
        $I->enableModule(2, 'wiki');
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Space');
        $I->amOnSpace(2);

        $I->showWikiPageInContainerMenu('First Public Space Wiki Page', '#space-main-menu');
        $I->showWikiPageInContainerMenu('First Private Space Wiki Page', '#space-main-menu');
    }

    public function testWikiPageInProfileMenu(AcceptanceTester $I)
    {
        $I->amUser1();

        $I->enableWikiOnProfile();

        $I->amOnUser1Profile();

        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
        $I->click('Let\'s go!');

        $I->createWikiPages('Profile');
        $I->amOnProfile();

        $I->showWikiPageInContainerMenu('First Public Profile Wiki Page', '.profile-content .left-navigation');
        $I->showWikiPageInContainerMenu('First Private Profile Wiki Page', '.profile-content .left-navigation');
    }

    public function testPermissionEditPages(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(1, 'wiki');

        $I->amGoingTo('create a Wiki Page for test single permission "Edit pages" without "Administer pages"');
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('Get your very own knowledge base off the ground by being the first one to create a Wiki page!', 15);
        $I->click('Let\'s go!');
        $I->waitForText('Create new page', 30);
        $I->fillField('#wikipage-title', 'Single Edit pages permission');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', 'Wiki Page for test single permission "Edit pages" without "Administer pages"');
        $I->jsShow('.form-collapsible-fields.closed fieldset');
        $I->jsClick('#pageeditform-ispublic');
        $I->click('Save', '#wiki-page-edit form');
        $I->seeSuccess();

        $I->amOnSpace1();
        $I->waitForText('Single Edit pages permission', 15);

        $I->amGoingTo('check admin(with permission "Administer pages") can edit and delete the Wiki Page');
        $I->click('.preferences .dropdown-toggle', '[data-stream-entry]:nth-of-type(1)');
        $I->waitForText('Edit', 15, '.dropdown.show');
        $I->see('Delete', '.dropdown.show');
        $I->see('Topics', '.dropdown.show');
        $I->see('Change to "Private"', '.dropdown.show');
        $I->see('Lock comments', '.dropdown.show');
        $I->see('Pin to top', '.dropdown.show');

        $I->amGoingTo('check member(with permission "Edit pages") can only edit the Wiki Page');
        $I->amUser2(true);
        $I->amOnSpace1();
        $I->waitForText('Single Edit pages permission');
        $I->click('.preferences .dropdown-toggle', '[data-stream-entry]:nth-of-type(1)');
        $I->waitForText('Edit', 15, '.dropdown.show');
        $I->dontSee('Delete', '.dropdown.show');
        $I->dontSee('Topics', '.dropdown.show');
        $I->dontSee('Change to "Private"', '.dropdown.show');
        $I->dontSee('Lock comments', '.dropdown.show');
        $I->dontSee('Pin to top', '.dropdown.show');
        $I->click('Edit', '[data-stream-entry]:nth-of-type(1) .dropdown.show');
        $I->waitForText('Edit page', 15, '.wiki-page-title');
        $I->seeElement('#wikipage-title[disabled]');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', 'Updated: Wiki Page for test single permission "Edit pages" without "Administer pages"');
        $I->click('Save', '#wiki-page-edit form');
        $I->seeSuccess();
        $I->waitForText('Updated: Wiki Page for test single permission', 15, '.wiki-page-body');
    }
}
