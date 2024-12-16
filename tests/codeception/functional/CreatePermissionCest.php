<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace wiki\functional;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use wiki\FunctionalPermissionTest;
use wiki\FunctionalTester;
use Yii;

class CreatePermissionCest extends FunctionalPermissionTest
{
    /**
     * @param FunctionalTester $I
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function testNonCreatePermissionView(FunctionalTester $I)
    {
        $I->wantTo('make sure users without create permission can\'t create pages');

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $I->assertSpaceAccessFalse(Space::USERGROUP_MEMBER, '/wiki/page/edit');

        $I->amAdmin(true);
        $category = $I->createWiki($space, 'Private Wiki', 'My private wiki content');
        $I->createWiki($space, 'Wiki Page', 'Wiki page content', ['category' => $category->id]);

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->seeCategory('Private Wiki');
        $I->click('Private Wiki', '.page-title');

        $I->seeInMenu('Index');
        $I->seeInMenu('Page History');
        $I->seeInMenu('Permalink');
        $I->see('My private wiki content');
        $I->dontSeeInMenu('Edit');
        $I->dontSeeInMenu('Delete');
    }

    public function testCreatePermissionCreateAndEditOwn(FunctionalTester $I)
    {
        $I->wantTo('test the creation end editing of a wiki page for members with create permission');

        Yii::$app->getModule('user')->settings->set('auth.internalUsersCanInvite', 1);

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_ALLOW);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->seeInitPageWithCreateOption();

        $I->click('Let\'s go!');
        $I->see('Create new page');

        $I->dontSeeElement('#wikipage-title:disabled');
        $I->dontSeeElement('#wikipage-parent_page_id:disabled');
        $I->dontSeeElement('#pageeditform-topics:disabled');
        $I->seeElement('#wikipage-is_home:disabled');
        $I->seeElement('#wikipage-admin_only:disabled');
        $I->seeElement('#pageeditform-ispublic:disabled');

        $I->see('In order to edit all fields, you need the permission to administer wiki pages.');

        $I->createWiki($space->guid, 'My own wiki', 'My own wiki content');

        $I->seeInMenu('Edit');
        $I->click('Edit');

        // Since I'am the content owner I can change the title
        $I->dontSee('#wikipage-title:disabled');
    }

    public function testCreatePermissionEditOthers(FunctionalTester $I)
    {
        $I->wantTo('test if a member with only create permission can edit content not created by them');

        Yii::$app->getModule('user')->settings->set('auth.internalUsersCanInvite', 1);

        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $space->permissionManager->clear();
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePage::class, BasePermission::STATE_ALLOW);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, EditPages::class, BasePermission::STATE_DENY);
        $space->permissionManager->setGroupState(Space::USERGROUP_MEMBER, AdministerPages::class, BasePermission::STATE_DENY);

        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');

        $category = $I->createWiki($space, 'Admin Category', 'Admin Category content');
        $I->createWiki($space, 'Wiki Page', 'Wiki page content', ['category' => $category->id]);

        $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER, '/wiki/overview');

        $I->seeCategory('Admin Category');

        $I->dontSeeElement('.drag-icon');
        $I->seeElement('a.wiki-category-add');

        $I->click('Admin Category');
        $I->dontSeeInMenu('Edit');
        $I->dontSeeInMenu('Delete');


        $I->amOnSpace($space->guid, '/wiki/page/edit', ['id' => $category->id]);
        $I->seeResponseCodeIs(403);
    }
}
