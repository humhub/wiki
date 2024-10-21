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

        // Go to page view
        $I->amOnSpace($space->guid, '/wiki/page/view', ['id' => $category->id, 'title' => $category->title]);

        // Get the current module
        $module = Yii::$app->getModule('wiki');                      
        // Get the current user
        $user = Yii::$app->user->identity;
        // Retrieve the current numbering state for this user from the settings
        $numberingEnabled = $module->settings->contentContainer($user)->get('wikiNumbering', 'disabled') === 'enabled';

        // Check if the numbering button is present and shows "Enable Numbering" when disabled
        $I->seeElement('a.toggle-numbering');
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        } 

        $I->click('a.toggle-numbering');

        // Retrieve the current numbering state for this user from the settings
        $numberingEnabled = $module->settings->contentContainer($user)->get('wikiNumbering', 'disabled') === 'enabled';
        
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        }

        $I->wantTo('check if the numbering button toggles between enabled and disabled states at overview');

        // Go to overview page
        $I->amOnSpace($space->guid, '/wiki/overview/list-categories');

        // Retrieve the current numbering state for this user from the settings
        $numberingEnabled = $module->settings->contentContainer($user)->get('overviewNumbering', 'disabled') === 'enabled';

        // Check if the numbering button is present and shows "Enable Numbering" when disabled
        $I->seeElement('a.toggle-numbering');
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        } 
        $I->click('a.toggle-numbering');

        // Retrieve the current numbering state for this user from the settings
        $numberingEnabled = $module->settings->contentContainer($user)->get('overviewNumbering', 'disabled') === 'enabled';
        
        if ($numberingEnabled) {
            $I->see('Disable Numbering', 'a.toggle-numbering');
        }   
        else {
            $I->see('Enable Numbering', 'a.toggle-numbering');
        } 
    }
}