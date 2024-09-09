<?php

namespace wiki\api;

use wiki\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class ListCest extends HumHubApiTestCest
{
    public function testEmptyList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see empty wiki pages list');
        $I->amAdmin();
        $I->seePaginationWikiPagesResponse('wiki', []);
    }

    public function testFilledList(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see sample created wiki pages list');
        $I->amAdmin();
        $I->createWikiPage('First wiki category', 'Sample content for the first wiki page-category.', ['isCategory' => 1]);
        $I->createWikiPage('1st wiki page', 'Sample content for the 1st wiki page.', ['parentPageId' => 1]);
        $I->createWikiPage('2nd wiki page', 'Sample content for the 2nd wiki page.', ['parentPageId' => 1]);
        $I->createWikiPage('Wiki sub-category', 'Sample content for the wiki sub-category.', ['isCategory' => 1, 'parentPageId' => 1]);
        $I->createWikiPage('1. Wiki page of the sub-category', 'Sample content for the first wiki page of the sub-category.', ['parentPageId' => 4]);
        $I->createWikiPage('2. Wiki page of the sub-category', 'Sample content for the second wiki page of the sub-category.', ['parentPageId' => 4]);
        $I->createWikiPage('3. Wiki page of the sub-category', 'Sample content for the third wiki page of the sub-category.', ['parentPageId' => 4]);
        $I->createWikiPage('Home wiki page', 'Sample content for home wiki page.', ['isHome' => 1]);
        $I->seePaginationWikiPagesResponse('wiki', [1, 2, 3, 4, 5, 6, 7, 8]);
    }

    public function testListByContainer(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see wiki pages by container');
        $I->amAdmin();
        $I->sendGet('wiki/container/123');
        $I->seeNotFoundMessage('Content container not found!');

        $I->createWikiPage('Sample wiki page title 1', 'Sample wiki page content 1', ['containerId' => 1]);
        $I->createWikiPage('Sample wiki page title 2', 'Sample wiki page content 2', ['containerId' => 4]);
        $I->createWikiPage('Sample wiki page title 3', 'Sample wiki page content 3', ['containerId' => 6]);
        $I->createWikiPage('Sample wiki page title 4', 'Sample wiki page content 4', ['containerId' => 4]);
        $I->createWikiPage('Sample wiki page title 5', 'Sample wiki page content 5', ['containerId' => 7]);
        $I->createWikiPage('Sample wiki page title 6', 'Sample wiki page content 6', ['containerId' => 4]);

        $I->seePaginationWikiPagesResponse('wiki/container/1', [1]);
        $I->seePaginationWikiPagesResponse('wiki/container/4', [2, 4, 6]);
        $I->seePaginationWikiPagesResponse('wiki/container/6', [3]);
        $I->seePaginationWikiPagesResponse('wiki/container/7', [5]);
    }

    public function testDeleteByContainer(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('delete wiki pages by container');
        $I->amAdmin();

        $I->createWikiPage('Sample wiki page title 1', 'Sample wiki page content 1', ['containerId' => 4]);
        $I->createWikiPage('Sample wiki page title 2', 'Sample wiki page content 2', ['containerId' => 4]);
        $I->createWikiPage('Sample wiki page title 3', 'Sample wiki page content 3', ['containerId' => 4]);

        $I->seePaginationWikiPagesResponse('wiki/container/4', [1, 2, 3]);
        $I->sendDelete('wiki/container/4');
        $I->seeSuccessMessage('3 records successfully deleted!');
        $I->seePaginationWikiPagesResponse('wiki/container/4', []);
    }
}
