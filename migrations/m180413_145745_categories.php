<?php

use yii\db\Migration;

class m180413_145745_categories extends Migration
{
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'is_category', $this->boolean()->defaultValue(false));
        $this->addColumn('wiki_page', 'parent_page_id', $this->integer()->defaultValue(null));
        $this->addForeignKey('wiki_page_parent', 'wiki_page', 'parent_page_id', 'wiki_page', 'id', 'SET NULL', 'SET NULL');
    }

    public function safeDown()
    {
        echo "m180413_145745_categories cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180413_145745_categories cannot be reverted.\n";

        return false;
    }
    */
}
