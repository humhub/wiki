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
        $I->wantTo('check if the numbering toggles between enabled and disabled');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_MEMBER);
        $I->amAdmin(true);
        $I->enableModule($space->guid, 'wiki');
        $category = $I->createWiki($space, 'Test Wiki Page', 'Test Wiki Page content');

        $I->amOnSpace($space->guid, '/wiki/container-config', ['id' => $category->id]);
        $I->seeElement('input#defaultsettings-wikinumberingenabled');
        $I->checkOption('input#defaultsettings-wikinumberingenabled');
        $I->seeElement('input#defaultsettings-overviewnumberingenabled');
        $I->checkOption('input#defaultsettings-overviewnumberingenabled');
        $I->click('Save', 'button');

        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');
        $I->seeElement('div.numbered');
        $I->amOnSpace($space->guid, '/wiki/page/view', ['id' => $category->id, 'title'=> $category->title]);
        $I->seeElement('div.numbered');

        $I->amOnSpace($space->guid, '/wiki/container-config', ['id' => $category->id]);
        $I->seeElement('input#defaultsettings-wikinumberingenabled');
        $I->uncheckOption('input#defaultsettings-wikinumberingenabled');
        $I->seeElement('input#defaultsettings-overviewnumberingenabled');
        $I->uncheckOption('input#defaultsettings-overviewnumberingenabled');
        $I->click('Save', 'button');
        
        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');
        $I->dontSeeElement('div.numbered');
        $I->amOnSpace($space->guid, '/wiki/page/view', ['id' => $category->id, 'title'=> $category->title]);
        $I->dontSeeElement('div.numbered');

    }
}