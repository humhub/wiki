<?php

use yii\db\Migration;

/**
 * Class m250606_033143_template_appendable_content
 */
class m250606_033143_template_appendable_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_template', 'is_appendable', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('wiki_template', 'appendable_content', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250606_033143_template_appendable_content cannot be reverted.\n";

        return false;
    }

}
