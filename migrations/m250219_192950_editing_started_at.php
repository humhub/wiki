<?php

use yii\db\Migration;

/**
 * Class m250219_192950_editing_started_at
 */
class m250219_192950_editing_started_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'editing_started_at', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('wiki_page', 'editing_started_at');
    }

}
