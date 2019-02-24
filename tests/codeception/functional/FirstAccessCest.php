<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\functional;

use wiki\FunctionalTester;

class FirstAccessCest
{

    /**
     * @param FunctionalTester $I
     */
    public function testFirstAccessForAdminUser(FunctionalTester $I)
    {
        $I->wantTo('make sure users without create permission can\'t create pages');

        $I->amAdmin(true);
        $I->enableModule(3, 'wiki');
        $I->amOnSpace3('/wiki/overview');

        $I->seeInitPageWithCreateOption();

        $I->click('Let\'s go!');
        $I->see('Create new page');

        $I->createCategoy(3, 'Private Wiki', 'My private wiki content');
    }

    public function testFirstAccessForNonCreatePermissionUser(FunctionalTester $I)
    {
        $I->wantTo('make sure users without create permission can\'t create pages');

        $I->amAdmin();
        $I->enableModule(1, 'wiki');

        $I->amUser1(true);

        $I->amOnSpace1('/wiki/overview');

        $I->seeInitPageWithoutCreateOption();
    }
}
