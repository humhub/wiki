<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\acceptance;

use humhub\modules\wiki\helpers\Url;
use wiki\AcceptanceTester;

class WikiCest
{

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testInstallAndCreatEntry(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(1, 'wiki');
        $I->click('Wiki', '.layout-nav-container');
        $I->waitForText('No pages created yet.');
        $I->click('Let\'s go!');

        $I->amGoingTo('Create my first wiki category page');
        $I->waitForText('Create new page');
        $I->fillField('#wikipage-title', 'First Test Wiki Category');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', '## My First Wiki Category!');
        $I->click('[for="wikipage-is_category"]');
        $I->click('Save');

        $I->waitForElementVisible('#wiki-page-content');
        $I->see('First Test Wiki Category');
        $I->see('My First Wiki Category!', 'h1');

        $I->wait(30);
    }
}