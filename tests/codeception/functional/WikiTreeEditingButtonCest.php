<?php

namespace wiki\functional;

use humhub\modules\space\models\Space;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\Module;
use humhub\modules\user\models\User;
use wiki\FunctionalTester;
use Yii;

class NumberingButtonCest
{
    public function testNumberingButtonHtml(FunctionalTester $I)
    {
        $I->wantTo('check if the wiki tree editing button toggles between enabled and disabled states at overview');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $category = $I->createWiki($space, 'Test Wiki Page', 'Test Wiki Page content');
        
        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');

        $I->seeElement('a.toggle-editing');
        $I->see('Enable wiki tree editing', 'a.toggle-editing');

        $I->click('a.toggle-editing');
        $I->see('Disable wiki tree editing', 'a.toggle-editing');

        $I->click('a.toggle-editing');
        $I->see('Enable wiki tree editing', 'a.toggle-editing');

    }
}