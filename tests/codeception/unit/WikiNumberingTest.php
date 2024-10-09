<?php
namespace humhub\modules\wiki\tests\unit;

use Yii;
use Codeception\Test\Unit;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\user\models\User;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\controllers\PageController;

class WikiNumberingTest extends Unit
{
    protected $user;
    protected $page;
    protected $module;
    protected $controller;
    protected $pageId;

    protected function _before() 
    {
        // Mock the user identity and login
        $this->user = $this->make(User::class, [
            'id' => 1,
            'username' => 'testUser',
        ]);
        Yii::$app->user->login($this->user);

        // Mock the WikiPage object
        $this->pageId = 123;
        $this->page = $this->make(WikiPage::class, [
            'id' => $this->pageId,
            'title' => 'Test Wiki Page',
        ]);

        // Mock the wiki module
        $this->module = Yii::$app->getModule('wiki');

        // Initiate the controller
        $this->controller = new PageController('wiki', $this->module);
        $this->controller->contentContainer = $this->user;
    }
    public function testToggleWikiNumberingEnabled()
    {
        // Mock wiki module settings for the user
        $this->module->settings->contentContainer($this->user)->set('wikiNumbering', 'disabled');

        // Call the actionToggleNumbering method
        $this->controller->actionToggleNumbering($this->pageId);

        // Assert that the state was updated to 'enabled'
        $this->assertEquals('enabled', $this->module->settings->contentContainer($this->user)->get('wikiNumbering'));

        // Call the logic that renders the button with the text
        $numberingEnabled = $this->module->settings->contentContainer($this->user)->get('wikiNumbering') === 'enabled';

        // Simulate rendering the button HTML
        $html = $this->renderButton($numberingEnabled);

        // Expected button label when numbering is enabled
        $expectedLabel = 'Disable Numbering';

        // Assert that the correct label is present in the rendered HTML
        $this->assertStringContainsString($expectedLabel, $html);
    }

    public function testToggleOverviewNumberingDisabled()
    {
        // Mock wiki module settings for the user
        $this->module->settings->contentContainer($this->user)->set('wikiNumbering', 'enabled');

        // Call the actionToggleNumbering method
        $this->controller->actionToggleNumbering($this->pageId);

        // Assert that the state was updated to 'disabled'
        $this->assertEquals('disabled', $this->module->settings->contentContainer($this->user)->get('wikiNumbering'));

        // Call the logic that renders the button with the text
        $numberingEnabled = $this->module->settings->contentContainer($this->user)->get('wikiNumbering') === 'enabled';

        // Simulate rendering the button HTML
        $html = $this->renderButton($numberingEnabled);

        // Expected button label when numbering is enabled
        $expectedLabel = 'Enable Numbering';

        // Assert that the correct label is present in the rendered HTML
        $this->assertStringContainsString($expectedLabel, $html);
    }

    // Function to render html button
    private function renderButton($numberingEnabled)
    {
        // Simulate what would be inside the view
        ob_start(); // Start output buffering
        ?>
        <a href="<?= Url::to(['/wiki/page/toggle-numbering']) ?>" class="btn btn-info btn-sm">
            <?= $numberingEnabled ? Yii::t('WikiModule.base', 'Disable Numbering') : Yii::t('WikiModule.base', 'Enable Numbering') ?>
        </a>
        <?php
        return ob_get_clean(); // Get the rendered content and stop buffering
    }
}
