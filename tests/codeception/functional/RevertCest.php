<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\functional;

use Codeception\Util\Locator;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\permissions\ViewHistory;
use Yii;
use wiki\FunctionalPermissionTest;
use wiki\FunctionalTester;
use humhub\libs\BasePermission;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\space\models\Space;
use yii\web\Response;

class RevertCest extends FunctionalPermissionTest
{
    public function testHistoryAccess(FunctionalTester $I)
    {
        $I->wantTo('check the history access for users with or without viewhistory permission');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, ViewHistory::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createCategoy($space, 'Admin Category', 'Admin Category content');
        $I->click('Edit page');
        $I->fillField('WikiPageRevision[content]', 'Edited Admin Category content');
        $I->saveWiki();


        $I->see('Page History');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');
        $I->click($category->title);
        $I->dontSeeInMenu('Page History');

        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, ViewHistory::class, BasePermission::STATE_ALLOW);

        $I->amOnSpace($space->guid, '/wiki/page/view', ['title' => $category->title]);
        $I->seeInMenu('Page History');
        $I->click('Page History');

        $I->see('Page history', 'h1');
    }
    public function testRevertWithEditPermission(FunctionalTester $I)
    {
        $I->wantTo('make user with edit permissions can revert wikis');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, ViewHistory::class, BasePermission::STATE_ALLOW);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createCategoy($space, 'Admin Category', 'Admin Category content');
        $I->click('Edit page');
        $I->fillField('WikiPageRevision[content]', 'Edited Admin Category content');
        $I->saveWiki();

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->amOnSpace($space->guid, '/wiki/page/view', ['title' => $category->title]);
        $I->click('Page History');

        $I->see('Page history', 'h1');

        $I->click(Locator::elementAt('.wiki-page-view-link', 2));

        $I->seeInCurrentUrl('revision');
        $I->dontSeeInMenu('Revert this');

        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_ALLOW);

        $I->amOnSpace($space->guid, '/wiki/page/view', ['title' => $category->title]);
        $I->click('Page History');
        $I->click(Locator::elementAt('.wiki-page-view-link', 2));
        $I->seeInCurrentUrl('revisionId');
        $I->seeInMenu('Revert this');

        $revisionid = Yii::$app->request->get('revisionId');

        $I->sendAjaxPostRequest(Url::toWikiRevertRevision($category, $revisionid));
        $I->seeSuccessResponseCode();
        Yii::$app->response->format = Response::FORMAT_HTML;

        $I->amOnSpace($space->guid, '/wiki/overview');
        $I->see('Admin Category');
        $I->click('Admin Category');
        $I->see('Admin Category content');
    }

    public function testRevertAdministerPermission(FunctionalTester $I)
    {
        $I->wantTo('make sure users with administer permission can revert users');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_ALLOW);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, ViewHistory::class, BasePermission::STATE_ALLOW);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createCategoy($space, 'Admin Category', 'Admin Category content');
        $I->click('Edit page');
        $I->fillField('WikiPageRevision[content]', 'Edited Admin Category content');
        $I->saveWiki();

        $I->see('Page History');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');
        $I->click($category->title);
        $I->click('Page History');

        $I->see('Page history', 'h1');

        $I->click(Locator::elementAt('.wiki-page-view-link', 2));
        $I->seeInMenu('Revert this');

        $revisionid = Yii::$app->request->get('revisionId');

        $I->sendAjaxPostRequest(Url::toWikiRevertRevision($category, $revisionid));
        $I->seeSuccessResponseCode();
        Yii::$app->response->format = Response::FORMAT_HTML;

        $I->amOnSpace($space->guid, '/wiki/overview');
        $I->see('Admin Category');
        $I->click('Admin Category');
        $I->see('Admin Category content');
    }

    public function testRevertProtected(FunctionalTester $I)
    {
        $I->wantTo('make sure protected pages can only be edited by admins');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_ALLOW);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, ViewHistory::class, BasePermission::STATE_ALLOW);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createCategoy($space, 'Admin Category', 'Admin Category content', ['admin_only' => 1]);
        $I->click('Edit page');
        $I->fillField('WikiPageRevision[content]', 'Edited Admin Category content');
        $I->saveWiki();

        $I->see('Page History');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');
        $I->click($category->title);
        $I->click('Page History');

        $I->see('Page history', 'h1');

        $I->click(Locator::elementAt('.wiki-page-view-link', 2));
        $I->dontSeeInMenu('Revert this');

        $I->sendAjaxPostRequest(Url::toWikiRevertRevision($category, Yii::$app->request->get('revisionId')));
        $I->seeResponseCodeIs(403);
    }
}
