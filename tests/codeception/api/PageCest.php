<?php

namespace wiki\api;

use Codeception\Util\HttpCode;
use wiki\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class PageCest extends HumHubApiTestCest
{
    public function testCreatePage(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('create a wiki page');
        $I->amAdmin();
        $I->createSampleWikiPage();
        $I->seeLastCreatedWikiPageDefinition();

        $I->amGoingTo('create a wiki page with error');
        $I->sendPost('wiki/container/1');
        $I->seeServerErrorMessage('Internal error while save valid wiki page!');
    }

    public function testGetWikiPageById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see wiki page by id');
        $I->amAdmin();
        $I->createSampleWikiPage();
        $I->sendGet('wiki/page/1');
        $I->seeWikiPageDefinitionById(1);
    }

    public function testUpdateWikiPageById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('update wiki page by id');
        $I->amAdmin();

        $I->sendPut('wiki/page/1');
        $I->seeNotFoundMessage('Page not found!');

        $I->createSampleWikiPage();
        $I->sendPut('wiki/page/1', [
            'WikiPage' => ['title' => 'Updated title'],
            'WikiPageRevision' => ['content' => 'Updated content.'],
        ]);
        $I->seeWikiPageDefinitionById(1);
    }

    public function testUpdateWikiPageWithCheckingLatestRevision(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('update wiki page with checking latest revision');
        $I->amAdmin();

        $I->amGoingTo('update with wrong latest revision');
        $I->createSampleWikiPage();
        $I->sendPut('wiki/page/1', [
            'WikiPage' => ['title' => 'Updated title'],
            'WikiPageRevision' => ['content' => 'Updated content.'],
            'PageEditForm' => ['latestRevisionNumber' => '1234567890'],
        ]);
        $I->seeCodeResponseContainsJson(HttpCode::UNPROCESSABLE_ENTITY, ['wikiForm' => ['confirmOverwriting' => ['']]]);

        $I->amGoingTo('update with correct latest revision');
        $I->sendPut('wiki/page/1', [
            'WikiPage' => ['title' => 'Updated title'],
            'WikiPageRevision' => ['content' => 'Updated content.'],
            'PageEditForm' => ['confirmOverwriting' => 1],
        ]);
        $I->seeWikiPageDefinitionById(1);
    }

    public function testDeleteWikiPageById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('delete wiki page by id');
        $I->amAdmin();

        $I->sendDelete('wiki/page/1');
        $I->seeNotFoundMessage('Content record not found!');

        $I->createSampleWikiPage();
        $I->sendDelete('wiki/page/1');
        $I->seeSuccessMessage('Successfully deleted!');
    }

    public function testChangeWikiPageIndex(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('change wiki page index');
        $I->amAdmin();

        $I->createWikiPage('1 wiki category', '1 wiki category content.', ['isCategory' => 1]);
        $I->createWikiPage('1.1 wiki page', '1.1 wiki page content.', ['parentPageId' => 1]);
        $I->createWikiPage('1.2 wiki page', '1.1 wiki page content.', ['parentPageId' => 1]);
        $I->createWikiPage('2 wiki category', '2 wiki category content.', ['isCategory' => 1]);
        $I->createWikiPage('2.1 wiki page', '2.1 wiki page content.', ['parentPageId' => 4]);

        $I->sendPatch('wiki/page/3/change-index', [
            'target_id' => 4,
            'index' => 123,
        ]);
        $I->seeSuccessMessage('Wiki page successfully moved!');
    }

    public function testMoveWikiPage(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('move wiki page');
        $I->amAdmin();
        $I->enableModule(1, 'wiki');

        $I->createSampleWikiPage();

        $I->sendPatch('wiki/page/1/move', ['target' => '5396d499-20d6-4233-800b-c6c86e5fa34a']);
        $I->seeSuccessMessage('Wiki page successfully moved!');
    }
}
