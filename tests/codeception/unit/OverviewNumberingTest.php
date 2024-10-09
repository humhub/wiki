<?php
namespace humhub\modules\wiki\tests\unit;

use Yii;
use Codeception\Test\Unit;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\user\models\User;
use humhub\modules\wiki\widgets\PageListItemTitle;
use humhub\modules\wiki\controllers\OverviewController;

class OverviewNumberingTest extends Unit
{
    protected $user;
    protected $module;
    protected $controller;
    protected function _before() 
    {
        // Mock the user identity and login
        $this->user = $this->make(User::class, [
            'id' => 1,
            'username' => 'testUser',
        ]);
        Yii::$app->user->login($this->user);

        // Mock the wiki module 
        $this->module = Yii::$app->getModule('wiki');

        // Initialise the controller
        $this->controller = new OverviewController('overview', $this->module);
        $this->controller->contentContainer = $this->user;
    }
    public function testGenerateNumbering()
    {
        // Create a new instance of PageListItemTitle
        $widget = new PageListItemTitle();

        // Test level 0 numbering
        $numbering = $widget->generateNumbering(0);
        $this->assertEquals('1', $numbering, 'Numbering at level 0 is incorrect.');

        // Test level 1 numbering
        $numbering = $widget->generateNumbering(1);
        $this->assertEquals('1.1', $numbering, 'Numbering at level 1 is incorrect.');

        // Test level 2 numbering
        $numbering = $widget->generateNumbering(2);
        $this->assertEquals('1.1.1', $numbering, 'Numbering at level 2 is incorrect.');

        // Reset and increment first level again
        $numbering = $widget->generateNumbering(0);
        $this->assertEquals('2', $numbering, 'Second increment of level 0 is incorrect.');
    }

   public function testToggleOverviewNumberingEnabled()
    {
        // Mock wiki module settings for the user
        $this->module->settings->contentContainer($this->user)->set('overviewNumbering', 'disabled');

        // Call the actionToggleNumbering method
        $this->controller->actionToggleNumbering();

        // Assert that the state was updated to 'enabled'
        $this->assertEquals('enabled', $this->module->settings->contentContainer($this->user)->get('overviewNumbering'));

        // Call the logic that renders the button with text
        $numberingEnabled = $this->module->settings->contentContainer($this->user)->get('overviewNumbering') === 'enabled';

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
        $this->module->settings->contentContainer($this->user)->set('overviewNumbering', 'enabled');

        // Call the actionToggleNumbering method
        $this->controller->actionToggleNumbering();

        // Assert that the state was updated to 'disabled'
        $this->assertEquals('disabled', $this->module->settings->contentContainer($this->user)->get('overviewNumbering'));

        // Call the logic that renders the button with text
        $numberingEnabled = $this->module->settings->contentContainer($this->user)->get('overviewNumbering') === 'enabled';

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
        <a href="<?= Url::to(['/wiki/overview/toggle-numbering']) ?>" class="btn btn-info btn-sm">
            <?= $numberingEnabled ? Yii::t('WikiModule.base', 'Disable Numbering') : Yii::t('WikiModule.base', 'Enable Numbering') ?>
        </a>
        <?php
        return ob_get_clean(); // Get the rendered content and stop buffering
    }
}
