<?php

use yii\db\Migration;

/**
 * Class m201125_034854_alter_content_type_in_wiki_page_review
 */
class m201125_034854_alter_content_type_in_wiki_page_review extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('wiki_page_revision', 'content', 'mediumtext');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('wiki_page_revision', 'content', 'text');
    }
}
