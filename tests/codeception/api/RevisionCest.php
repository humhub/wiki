<?php

namespace wiki\api;

use humhub\modules\wiki\helpers\RestDefinitions;
use humhub\modules\wiki\models\WikiPageRevision;
use wiki\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class RevisionCest extends HumHubApiTestCest
{
    public function testGetRevisionsByWikiPageId(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see revisions by wiki page id');
        $I->amAdmin();
        $I->createSampleWikiPageWithRevisions();
        $I->seePaginationGetResponse('wiki/page/1/revisions', [
            $this->getWikiPageRevisionDefinitionById(1),
            $this->getWikiPageRevisionDefinitionById(2),
            $this->getWikiPageRevisionDefinitionById(3),
        ]);
    }

    public function testGetRevisionById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see a revision by id');
        $I->amAdmin();
        $I->createSampleWikiPageWithRevisions();

        $I->sendGet('wiki/revision/3');
        $I->seeSuccessResponseContainsJson($this->getWikiPageRevisionDefinitionById(3));

        $I->sendGet('wiki/revision/1');
        $I->seeSuccessResponseContainsJson($this->getWikiPageRevisionDefinitionById(1));
    }

    public function testRevertPageByRevision(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('revert a wiki page by a revision');
        $I->amAdmin();
        $I->createSampleWikiPageWithRevisions();

        $I->sendPatch('wiki/revision/3/revert');
        $I->seeBadMessage('Revert not possible. Already latest revision!');

        $I->sendPatch('wiki/revision/2/revert');
        $I->seeSuccessMessage('Wiki page revision successfully reverted.');
    }

    private function getWikiPageRevisionDefinitionById($revisionId)
    {
        $revision = WikiPageRevision::findOne(['id' => $revisionId]);
        return ($revision ? RestDefinitions::getWikiPageRevision($revision) : []);
    }
}
