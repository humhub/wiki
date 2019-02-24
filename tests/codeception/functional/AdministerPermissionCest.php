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
use wiki\FunctionalPermissionTest;
use wiki\FunctionalTester;
use humhub\libs\BasePermission;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\space\models\Space;
use Yii;
use yii\web\Response;

class AdministerPermissionCest extends FunctionalPermissionTest
{
    public function testDragItem(FunctionalTester $I)
    {
        $I->wantTo('test if users with administer permission can drag items');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_ALLOW);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $I->createCategoy($space, 'Admin Category', 'Admin Category content');
        $I->createWiki($space, 'Admin Wiki', 'Admin Wiki content');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->seeElement('.drag-icon');
        $I->dontSeeCategory('a.wiki-category-add');
        $I->seeCategory('Pages without category');

        $I->sendAjaxPostRequest(Url::to(['/wiki/page/sort', 'cguid' => $space->guid]), ['ItemDrop[id]' => 2, 'ItemDrop[targetId]' => 1, 'ItemDrop[index]' => 0]);
        Yii::$app->response->format = Response::FORMAT_HTML;
        $I->amOnSpace($space->guid, '/wiki/overview');
        $I->dontSeeCategory('Pages without category');
    }

    public function testDragList(FunctionalTester $I)
    {
        $I->wantTo('test if users with administer permission can drag lists');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_ALLOW);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category1 = $I->createCategoy($space, 'Admin Category 1', 'Admin Category content');
        $I->createCategoy($space, 'Admin Category 2', 'Admin Category content');
        $I->createWiki($space, 'Admin Page 1', 'Admin Page content', ['category' => $category1->id]);

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->seeElement('.drag-icon');
        $I->dontSeeCategory('Pages without category');

        $I->see('Admin Category 1', Locator::firstElement('.page-category-title'));
        $I->see('Admin Category 2', Locator::elementAt('.page-category-title', 2));

        $I->sendAjaxPostRequest(Url::to(['/wiki/page/sort', 'cguid' => $space->guid]), ['ItemDrop[id]' => 2, 'ItemDrop[targetId]' => 1, 'ItemDrop[index]' => 0]);

        Yii::$app->response->format = Response::FORMAT_HTML;

        $I->amOnSpace($space->guid, '/wiki/overview');

        $I->see('Admin Category 2', Locator::firstElement('.page-category-title'));
        $I->see('Admin Category 1', Locator::elementAt('.page-category-title', 2));
        $I->see('Admin Page 1');
    }

    public function testEditPage(FunctionalTester $I)
    {
        $I->wantTo('test if users with administer permission can edit pages');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_ALLOW);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category1 = $I->createCategoy($space, 'Admin Category 1', 'Admin Category content');
        $I->createCategoy($space, 'Admin Category 2', 'Admin Category content');
        $I->createWiki($space, 'Admin Page 1', 'Admin Page content', ['category' => $category1->id]);

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->click('Admin Page 1');
        $I->seeInMenu('Edit page');
        $I->click('Edit page');

        $I->seeElement('#wikipage-title');
        $I->seeElement('#wikipage-parent_page_id'); // functional tests see display:none elements..
        $I->seeElement('#pageeditform-topics');
        $I->seeElement('#wikipage-is_home');
        $I->seeElement('#wikipage-admin_only');
        $I->seeElement('#wikipage-is_category');
        $I->seeElement('#pageeditform-ispublic');
        $I->dontSee('In order to edit all fields, you need the permission to administer wiki pages.');

        $I->fillField('WikiPage[title]', 'Changed to category');
        $I->fillField('WikiPageRevision[content]', 'Changed content');
        $I->checkOption('#wikipage-is_home');
        $I->checkOption('#wikipage-admin_only');
        $I->checkOption('#wikipage-is_category');
        $I->checkOption('#pageeditform-ispublic');

        $I->click('Save', '#wiki-page-edit');

        $I->seeInMenu('Home');
        $I->seeElement('.fa-globe');
        $I->see('Changed to category', '.wiki-page-content');
        $I->see('Changed content', '.wiki-page-content');
        $I->see('There are no pages in this category', '.wiki-page-content');
    }

    public function testEditProtected(FunctionalTester $I)
    {
        $I->wantTo('test if users with edit permission can edit protected content');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createCategoy($space, 'Admin Category 1', 'Admin Category content', ['admin_only' => true]);
        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $category->id]);
        $I->seeSuccessResponseCode();

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $category->id]);
        $I->seeResponseCodeIs(403);

        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_ALLOW);
        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $category->id]);
        $I->seeResponseCodeIs(200);
    }

    public function testDelete(FunctionalTester $I)
    {
        $I->wantTo('test if users with edit permission can edit others content');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $page = $I->createWiki($space, 'Admin Category 1', 'Admin Category content');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->sendAjaxPostRequest(Url::toWikiDelete($page));
        $I->seeResponseCodeIs(403);

        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_ALLOW);

        $I->sendAjaxPostRequest(Url::toWikiDelete($page));
        $I->seeSuccessResponseCode();
    }
}
