<?php

use humhub\components\Migration;

/**
 * Class m251129_232300_tree_title
 */
class m251129_232300_tree_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'tree_title', $this->string(255));
        $this->update('wiki_page', [
            'tree_title' => new \yii\db\Expression('title'),
        ], 'tree_title IS NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('wiki_page', 'tree_title');
    }
}
