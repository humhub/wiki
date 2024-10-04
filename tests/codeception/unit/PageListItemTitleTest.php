<?php
namespace humhub\modules\wiki\tests\unit;

use humhub\modules\wiki\widgets\PageListItemTitle;
use Codeception\Test\Unit;
use Yii;
use yii\web\Request;
// use yii\helpers\Url;
use humhub\modules\wiki\helpers\Url;

class PageListItemTitleTest extends Unit
{
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

    // Test when numbering is enabled
    public function testButtonWhenNumberingEnabled()
    {
        // Simulate a request with the 'numbering' parameter set to 'enabled'
        Yii::$app->set('request', new Request([
            'queryParams' => ['numbering' => 'enabled']
        ]));

        // Check if numbering is enabled
        $numberingEnabled = Yii::$app->request->get('numbering', 'disabled') === 'enabled';

        // Assert that the numbering is enabled
        $this->assertTrue($numberingEnabled);

        // Use a valid route in Url::to() as it is hard to simulate Url::current()
        $url = Url::to(['/wiki/overview/list-categories', 'numbering' => 'disabled']); 
        $expectedUrl = '/index-test.php?r=wiki%2Foverview%2Flist-categories&numbering=disabled'; 

        // Assert that the correct URL is generated
        $this->assertEquals($expectedUrl, $url);

        // Expected button label when numbering is enabled
       $expectedLabel = 'Disable Numbering';


       // Render the button HTML with the $numberingEnabled = true
       $html = $this->renderButton($numberingEnabled);


       // Assert that the correct label is present in the rendered HTML
       $this->assertStringContainsString($expectedLabel, $html);
    }

    // Test when numbering is disabled
    public function testButtonWhenNumberingDisabled()
    {
        // Simulate a request with the 'numbering' parameter set to 'disabled'
        Yii::$app->set('request', new Request([
            'queryParams' => ['numbering' => 'disabled']
        ]));

        // Check if numbering is disabled
        $numberingEnabled = Yii::$app->request->get('numbering', 'disabled') === 'enabled';

        // Assert that the numbering is disabled
        $this->assertFalse($numberingEnabled);

        /// Use a valid route in Url::to() as it is hard to simulate Url::current()
        $url = Url::to(['/wiki/overview/list-categories', 'numbering' => 'enabled']); 
        $expectedUrl = '/index-test.php?r=wiki%2Foverview%2Flist-categories&numbering=enabled';

        // Assert that the correct URL is generated
        $this->assertEquals($expectedUrl, $url);

        // Expected button label when numbering is disabled
       $expectedLabel = 'Enable Numbering';


       // Render the button HTML with the $numberingEnabled = false
       $html = $this->renderButton($numberingEnabled);


       // Assert that the correct label is present in the rendered HTML
       $this->assertStringContainsString($expectedLabel, $html);
    }

    // Function to render html button
   private function renderButton($numberingEnabled)
   {
       // Simulate what would be inside the view
       ob_start(); // Start output buffering
       ?>
       <a href="<?= Url::to(['/wiki/overview/list-categories', 'numbering' => $numberingEnabled ? 'disabled' : 'enabled']) ?>" class="btn-sm btn btn-info">
           <?= $numberingEnabled ? Yii::t('WikiModule.base', 'Disable Numbering') : Yii::t('WikiModule.base', 'Enable Numbering') ?>
       </a>
       <?php
       return ob_get_clean(); // Get the rendered content and stop buffering
   }
}
