<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\functional;

use Yii;
use wiki\FunctionalPermissionTest;
use wiki\FunctionalTester;
use humhub\libs\BasePermission;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\space\models\Space;

class EditPermissionCest extends FunctionalPermissionTest
{
    public function testEditPermission(FunctionalTester $I)
    {
        $I->wantToTest('if users with edit permission can edit others content');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_ALLOW);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $I->createCategoy($space, 'Admin Category', 'Admin Category content');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->dontSee('New Page');
        $I->dontSeeElement('New Page');
        $I->dontSeeElement('.drag-icon');
        $I->dontSeeElement('a.wiki-category-add');

        $I->seeCategory('Admin Category');
        $I->click('Admin Category');
        $I->seeInMenu('Edit page');
        $I->dontSeeInMenu('Delete');

        $I->click('Edit page');

        $I->seeElement('#wikipage-title:disabled');
        $I->dontSee('#wikipage-parent_page_id');
        $I->seeElement('#pageeditform-topics');
        $I->seeElement('#wikipage-is_home:disabled');
        $I->seeElement('#wikipage-admin_only:disabled');
        $I->seeElement('#wikipage-is_category:disabled');
        $I->seeElement('#pageeditform-ispublic:disabled');

        $I->fillField('WikiPageRevision[content]', 'Changed content');
        $I->click('Save', '#wiki-page-edit');

        $I->see('Changed content', '.wiki-page-content');
    }

    public function testAdminOnlyEdit(FunctionalTester $I)
    {
        $I->wantTo('make sure protected pages can only be edited by admins');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_ALLOW);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createCategoy($space, 'Admin Category', 'Admin Category content', ['admin_only' => 1]);
        $I->seeInMenu('Edit page');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');
        $I->seeCategory('Admin Category');
        $I->click('Admin Category');
        $I->dontSeeInMenu('Edit page');
        $I->dontSeeInMenu('Delete');
        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $category->id]);
        $I->seeResponseCodeIs(403);
    }
}
