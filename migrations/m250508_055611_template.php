<?php

use yii\db\Migration;

/**
 * Class m250508_055611_template
 */
class m250508_055611_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('wiki_template', [
            'id' => $this->primaryKey(),
            'contentcontainer_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'title_template' => $this->string()->notNull(),
            'content' => $this->text()->null(),
            'placeholders' => $this->json()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        // Index + Foreign Key for contentcontainer_id
        $this->createIndex('idx-wiki_template-contentcontainer_id', 'wiki_template', 'contentcontainer_id');
        $this->addForeignKey('fk-wiki_template-contentcontainer', 'wiki_template', 'contentcontainer_id', 'contentcontainer', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-wiki_template-contentcontainer', 'wiki_template');
        $this->dropIndex('idx-wiki_template-contentcontainer_id', 'wiki_template');
        $this->dropTable('wiki_template');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250508_055611_template cannot be reverted.\n";

        return false;
    }
    */
}
