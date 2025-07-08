<?php

namespace wiki\functional;

use humhub\modules\wiki\models\WikiTemplate;
use Codeception\Util\Locator;
use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\permissions\CreatePage;
use humhub\modules\wiki\permissions\EditPages;
use humhub\modules\wiki\permissions\ViewHistory;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;
use wiki\FunctionalPermissionTest;
use wiki\FunctionalTester;
use Codeception\Test\Unit;
use Yii;
use yii\web\Response;

class TemplateCrudCest
{

    public function testCreateTemplate(FunctionalTester $I)
    {
        $I->wantTo('Check creation of a new template');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->enableModule($space->guid, 'wiki');
        
        $I->amOnSpace($space->guid, '/wiki/template/index');
        $I->see('Manage Templates');

        $I->click('Create Template');
        $I->fillField('WikiTemplate[title]', 'First Template');
        $I->fillField('WikiTemplate[content]', 'Content of First Template');
        $I->click('Save');
        $I->see('Manage Templates');
        $I->see('First Template');
    }

    public function testEditTemplate(FunctionalTester $I)
    {
        $I->wantTo('Check editing of a template');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->enableModule($space->guid, 'wiki');
        $I->amOnSpace($space->guid, '/wiki/template/index');
        $I->see('First Template');
        $I->click('a.edit-template');
        $I->fillField('WikiTemplate[title]', 'Edited First Template');
        $I->fillField('WikiTemplate[content]', 'Content of First Template');
        $I->click('Save');
        $I->see('Manage Templates');
        $I->see('Edited First Template');
    }

    public function testDeleteTemplate(FunctionalTester $I)
    {
        $I->wantTo('Check deletion of a template');
        $space = $I->loginBySpaceUserGroup(Space::USERGROUP_ADMIN);
        $I->enableModule($space->guid, 'wiki');
        
        $I->amOnSpace($space->guid, '/wiki/template/index');
        $I->see('Edited First Template');
        $I->click('a.delete-template');
        $I->dontsee('Edited First Template');
    }
}
