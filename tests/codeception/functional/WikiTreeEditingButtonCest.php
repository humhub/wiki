<?php

namespace wiki\functional;

use humhub\modules\space\models\Space;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\Module;
use humhub\modules\user\models\User;
use wiki\FunctionalTester;
use Yii;

class EditingButtonCest
{
    public function testEditingButtonHtml(FunctionalTester $I)
    {
        $I->wantTo('check if the wiki tree editing button toggles between enabled and disabled states at overview');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->enableModule($space->guid, 'wiki');
        $category = $I->createWiki($space, 'Test Wiki Page', 'Test Wiki Page content');
        
        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');

        $I->seeElement('a.toggle-editing');
        $I->see('Enable wiki tree editing', 'a.toggle-editing');
        $I->dontSeeElement('.drag-icon');

        $I->click('a.toggle-editing');
        $I->see('Disable wiki tree editing', 'a.toggle-editing');
        $I->seeElement('.drag-icon');

        $I->click('a.toggle-editing');
        $I->see('Enable wiki tree editing', 'a.toggle-editing');
        $I->dontSeeElement('.drag-icon');
    }

    public function testEditingButtonVisibilityForMember(FunctionalTester $I)
    {
        $I->wantTo('Check if editing button is not visible for member');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $I->enableModule($space->guid, 'wiki');
        $category = $I->createWiki($space, 'Test Wiki Page', 'Test Wiki Page content');

        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');
        $I->dontSeeElement('a.toggle-editing');
    }

    public function testEditingButtonVisibilityForModerator(FunctionalTester $I)
    {
        $I->wantTo('Check if editing button is visible for moderator');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MODERATOR);
        $I->enableModule($space->guid, 'wiki');
        $category = $I->createWiki($space, 'Test Wiki Page', 'Test Wiki Page content');

        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');
        $I->seeElement('a.toggle-editing');
    }
}