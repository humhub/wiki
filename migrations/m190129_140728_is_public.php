<?php

use yii\db\Migration;

/**
 * Class m190129_140728_is_public
 */
class m190129_140728_is_public extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'is_public', 'tinyint(4) NOT NULL DEFAULT 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190129_140728_is_public cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190129_140728_is_public cannot be reverted.\n";

        return false;
    }
    */
}
