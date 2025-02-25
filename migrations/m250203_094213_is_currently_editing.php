<?php

use yii\db\Migration;

/**
 * Class m250203_094213_is_currently_editing
 */
class m250203_094213_is_currently_editing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'is_currently_editing', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->dropColumn('wiki_page', 'is_currently_editing');

    }
}
