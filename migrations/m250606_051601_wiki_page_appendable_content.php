<?php

use yii\db\Migration;

/**
 * Class m250606_051601_wiki_page_appendable_content
 */
class m250606_051601_wiki_page_appendable_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'is_appendable', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('wiki_page', 'appendable_content', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250606_051601_wiki_page_appendable_content cannot be reverted.\n";

        return false;
    }
    
}
