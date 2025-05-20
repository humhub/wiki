<?php

namespace functional;

use humhub\modules\wiki\models\WikiTemplate;
use FunctionalTester;

class TemplateCrudCest
{
    public function _before(FunctionalTester $I)
    {
        // optional login if needed
    }

    public function testCreateTemplate(FunctionalTester $I)
    {
        $I->amOnPage('/template/create');
        $I->submitForm('#template-form', [
            'Template[title]' => 'Test Template',
            'Template[content]' => '<p>Hello World</p>',
        ]);
        $I->see('Test Template');
        $I->seeInDatabase('template', ['title' => 'Test Template']);
    }

    public function testViewTemplate(FunctionalTester $I)
    {
        $template = WikiTemplate::find()->one();
        $I->amOnPage("/template/view?id={$template->id}");
        $I->see($template->title);
        $I->see($template->content);
    }

    public function testUpdateTemplate(FunctionalTester $I)
    {
        $template = WikiTemplate::find()->one();
        $I->amOnPage("/template/update?id={$template->id}");
        $I->submitForm('#template-form', [
            'Template[title]' => 'Updated Title',
        ]);
        $I->see('Updated Title');
        $I->seeInDatabase('template', ['id' => $template->id, 'title' => 'Updated Title']);
    }

    public function testDeleteTemplate(FunctionalTester $I)
    {
        $template = new WikiTemplate();
        $template->title = 'Delete Me';
        $template->content = 'To be deleted';
        $template->save();

        $I->amOnPage("/template/delete?id={$template->id}");
        $I->dontSeeInDatabase('template', ['id' => $template->id]);
    }
}
