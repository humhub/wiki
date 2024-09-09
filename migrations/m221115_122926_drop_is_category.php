<?php

use humhub\components\Migration;

/**
 * Class m221115_122926_drop_is_category
 */
class m221115_122926_drop_is_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->safeDropColumn('wiki_page', 'is_category');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->safeAddColumn('wiki_page', 'is_category', $this->boolean()->defaultValue(false));
    }
}
