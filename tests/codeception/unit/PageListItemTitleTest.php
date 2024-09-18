<?php
namespace humhub\modules\wiki\tests\unit;

use humhub\modules\wiki\widgets\PageListItemTitle;
use Codeception\Test\Unit;

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
}
