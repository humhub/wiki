<?php

use yii\db\Migration;

/**
 * Class m250617_083333_template_appendable_content_placeholder
 */
class m250617_083333_template_appendable_content_placeholder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_template', 'appendable_content_placeholder', $this->json()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250617_083333_template_appendable_content_placeholder cannot be reverted.\n";

        return false;
    }

}
