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
        $I->wantTo('check if the numbering button toggles between enabled and disabled states at page view');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $category = $I->createWiki($space, 'Test Wiki Page', 'Test Wiki Page content');

        $I->amOnSpace($space->guid, '/wiki/page/view', ['id' => $category->id, 'title' => $category->title]);

        $I->seeElement('a.toggle-numbering');
        $I->see('Enable Numbering', 'a.toggle-numbering');
        $I->click('a.toggle-numbering');
        $I->see('Disable Numbering', 'a.toggle-numbering');
        $I->click('a.toggle-numbering');
        $I->see('Enable Numbering', 'a.toggle-numbering');

        $I->wantTo('check if the numbering button toggles between enabled and disabled states at overview');

        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');

        $I->seeElement('a.toggle-numbering');
        $I->see('Enable Numbering', 'a.toggle-numbering');
        $I->click('a.toggle-numbering');
        $I->see('Disable Numbering', 'a.toggle-numbering');
        $I->click('a.toggle-numbering');
        $I->see('Enable Numbering', 'a.toggle-numbering');
    }
}