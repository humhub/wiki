<?php

use yii\db\Migration;

/**
 * Class m180907_224038_page_sort_order
 */
class m180907_224038_page_sort_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'sort_order', 'int(11) DEFAULT 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180907_224038_page_sort_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180907_224038_page_sort_order cannot be reverted.\n";

        return false;
    }
    */
}
