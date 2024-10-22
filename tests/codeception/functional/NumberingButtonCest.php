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

        $module = Yii::$app->getModule('wiki');                      
        $user = Yii::$app->user->identity;
        $numberingEnabled = $module->settings->contentContainer($user)->get('wikiNumberingEnabled');

        $I->seeElement('a.toggle-numbering');
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        } 

        $I->click('a.toggle-numbering');

        $numberingEnabled = $module->settings->contentContainer($user)->get('wikiNumberingEnabled');
        
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        }

        $I->wantTo('check if the numbering button toggles between enabled and disabled states at overview');

        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');

        $numberingEnabled = $module->settings->contentContainer($user)->get('overviewNumberingEnabled');

        $I->seeElement('a.toggle-numbering');
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        } 
        $I->click('a.toggle-numbering');

        $numberingEnabled = $module->settings->contentContainer($user)->get('overviewNumberingEnabled');
        
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        } 
    }
}