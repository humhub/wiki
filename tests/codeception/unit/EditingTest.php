<?php
namespace humhub\modules\wiki\tests\unit;

use Yii;
use Codeception\Test\Unit;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\user\models\User;
use humhub\modules\wiki\models\WikiPage;

class IsEditingTest extends Unit
{
    public function testIsEditing()
    {
        $wikiPage = new WikiPage();
        $wikiPage->is_currently_editing = null;

        // No one is editing
        $this->assertFalse($wikiPage->isEditing());

        // Simulate another user editing
        $wikiPage->is_currently_editing = 2; // Assume user ID 2 is editing
        Yii::$app->user->identity = User::findOne(1); // Simulate logged-in user with ID 1

        $this->assertTrue($wikiPage->isEditing());

        // If the logged-in user is the editor
        Yii::$app->user->identity = User::findOne(2);
        $this->assertFalse($wikiPage->isEditing());
    }

    public function testUpdateIsEditing()
    {
        $wikiPage = new WikiPage();
        Yii::$app->user->identity = User::findOne(1); // Assume user ID 1 is logged in

        $this->assertTrue($wikiPage->updateIsEditing());
        $this->assertEquals(1, $wikiPage->is_currently_editing);
    }

    public function testDoneEditing()
    {
        $wikiPage = new WikiPage();
        Yii::$app->user->identity = User::findOne(1); // Assume user ID 1 is logged in

        $wikiPage->is_currently_editing = 1;
        $this->assertTrue($wikiPage->doneEditing());
        $this->assertNull($wikiPage->is_currently_editing);

        // Simulate another user trying to "doneEditing"
        $wikiPage->is_currently_editing = 2;
        $this->assertNull($wikiPage->doneEditing()); // Should not clear the field
        $this->assertEquals(2, $wikiPage->is_currently_editing);
    }
}
