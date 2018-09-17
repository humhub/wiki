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

        /**
         * CREATE CATEGORY
         */
        $I->amGoingTo('Create my first wiki category page');
        $I->waitForText('Create new page');
        $I->fillField('#wikipage-title', 'First Test Wiki Category');
        $I->fillField('#wikipagerevision-content .humhub-ui-richtext', '# My First Wiki Category!');
        $I->click('[for="wikipage-is_category"]');
        $I->click('Save');

        $I->waitForElementVisible('.wiki-page-content');
        $I->see('First Test Wiki Category');
        $I->wait(1);
        $I->see('My First Wiki Category!', 'h1');

        $I->click('#wiki_index');
        $I->waitForText('Index', null, '.wiki-content');
        $I->see('First Test Wiki Category', '.wiki-page-list');


        /**
         * CREATE First SUB PAGE
         */
        $I->amGoingTo('Create my first sub page');
        $I->jsClick('[data-original-title="Add Page"]');
        $I->waitForText('Create new page');

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
        $I->waitForText('Create new page');

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
        $I->see('Home', '.wiki-menu');
        $I->click('Wiki', '.layout-nav-container');

        $I->waitForText('My Sub Page', null,'.wiki-content');

        /**
         * Move Category
         */
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