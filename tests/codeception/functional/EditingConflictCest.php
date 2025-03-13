<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\functional;

use Codeception\Util\Locator;
use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\wiki\permissions\ViewHistory;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use wiki\FunctionalPermissionTest;
use wiki\FunctionalTester;
use Codeception\Test\Unit;
use Yii;
use yii\web\Response;

class PageControllerCest
{
    public function testMergeFunctionality(FunctionalTester $I)
    {
        $I->wantTo('Ensure merging wiki page revisions works correctly');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');

        $I->sendAjaxPostRequest(Url::toWikiMerge($page), [
            'WikiPage' => ['title' => 'Updated title'],
            'WikiPageRevision' => ['content' => 'Updated content.'],
            'PageEditForm' => ['latestRevisionNumber' => '1234567890'],]);
        
        $I->amOnSpace($space->guid, '/wiki/page/view', ['id' => $page->id, 'title' => $page->title]);
        $I->see('Initial content');
        $I->see('conflicting changes');
        $I->see('Updated content');

    }

    public function testCreateCopyFunctionality(FunctionalTester $I) 
    {
        $I->wantTo('Ensure Creating copy of wiki page revisions works correctly');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');
    
        $I->sendAjaxPostRequest(Url::toWikiCreateCopy($page), [
            'WikiPage' => ['title' => 'Test Page'],
            'WikiPageRevision' => ['content' => 'Updated content.'],
            'PageEditForm' => ['latestRevisionNumber' => '1234567890'],]);
        
        $I->amOnSpace($space->guid, '/wiki/overview');
        $I->see('Test page conflicting copy of');

        $I->amOnSpace($space->guid, '/wiki/page/view', ['id' => 2]);
        $I->see('Test page conflicting copy of');
        $I->see('Updated content.');
        $I->dontSee('Initial content');
    }


    public function testEditingStatusSameUser(FunctionalTester $I)
    {
        $I->wantTo('Check the editing status of an existing Wiki page when access by same user');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');
        
        $I->amOnSpace($space->guid, '/wiki/page/editing-status', ['id' => $page->id]);

        $I->seeResponseCodeIs(200);

        $I->seeInSource('"success":true');  
        $I->seeInSource('"isEditing":false');
        $I->seeInSource('"user":null');

        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $page->id]);
        $I->amOnSpace($space->guid, '/wiki/page/editing-status', ['id' => $page->id]);
        $I->seeResponseCodeIs(200);

        $I->seeInSource('"success":true');
        $I->seeInSource('"isEditing":false');
        $I->seeInSource('"user":"admin"');

        $I->amOnSpace($space->guid, '/wiki/overview');
        $I->sendAjaxPostRequest(Url::toWikiDelete($page));
        $I->seeSuccessResponseCode();
        
        $I->amOnSpace($space->guid, '/wiki/page/editing-status', ['id' => $page->id]);

        $I->seeResponseCodeIs(404);

    }

    public function testEditingStatusSave(FunctionalTester $I)
    {
        $I->wantTo('Check the editing status of an existing Wiki page when access by same user');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');

        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $page->id]);
        $I->saveWiki();
        $I->amOnSpace($space->guid, '/wiki/page/editing-status', ['id' => $page->id]);
        $I->seeResponseCodeIs(200);

        $I->seeInSource('"success":true');  
        $I->seeInSource('"isEditing":false');
        $I->seeInSource('"user":null');

    }

    public function testEditingStatusDiffUser(FunctionalTester $I)
    {
        $I->wantTo('Check the editing status of an existing Wiki page when access by different user');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MODERATOR);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');

        $page->updateAttributes(['is_currently_editing' => 'admin', 'editing_started_at' => time()]);
        $I->amOnSpace($space->guid, '/wiki/page/editing-status', ['id' => $page->id]);

        $I->seeResponseCodeIs(200);

        $I->seeInSource('"success":true'); 
        $I->seeInSource('"isEditing":true');
        $I->seeInSource('"user":"admin"');

    }
 
    public function testEditingStatusPageNotFound(FunctionalTester $I)
    {   
        $I->wantTo('Check the editing status of an non-existing Wiki page when access by different user');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $I->amOnSpace($space->guid, '/wiki/page/editing-status', ['id' => 123456789]);
        $I->seeResponseCodeIs(404);
    }

    public function testEditingTTLSameUser(FunctionalTester $I)
    {
        $I->wantTo('Check the editing TTL status of a Wiki page when access by same user');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');
        

        $I->amOnSpace($space->guid, '/wiki/page/editing-timer-update', ['id' => $page->id]);

        $I->seeResponseCodeIs(200);

        $I->seeInSource('"success":true');
        $I->seeInSource('"user":"admin"');
        $I->seeInSource('"unAuthorizedLogin":false');

    }

    public function testEditingTTLDiffUser(FunctionalTester $I)
    {
        $I->wantTo('Check the editing TTL status of a Wiki page when access by different user');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MODERATOR);
        $I->enableModule($space->guid, 'wiki');
        $page = $I->createWiki($space, 'Test Page', 'Initial content');

        $page->updateAttributes(['is_currently_editing' => 'admin', 'editing_started_at' => time()]);
        $I->amOnSpace($space->guid, '/wiki/page/editing-timer-update', ['id' => $page->id]);

        $I->seeResponseCodeIs(200);

        $I->seeInSource('"success":true');
        $I->seeInSource('"user":"admin"');
        $I->seeInSource('"unAuthorizedLogin":true');

    }

}