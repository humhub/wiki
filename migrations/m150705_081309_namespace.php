<?php

use humhub\components\Migration;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\models\WikiPageRevision;

class m150705_081309_namespace extends Migration
{

    public function up()
    {
        $this->renameClass('WikiPage', WikiPage::className());
        $this->renameClass('WikiPageRevision', WikiPageRevision::className());
    }

    public function down()
    {
        echo "m150705_081309_namespace cannot be reverted.\n";

        return false;
    }

}
