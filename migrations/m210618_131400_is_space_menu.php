<?php

use yii\db\Migration;

/**
 * Class m210618_131400_is_space_menu
 */
class m210618_131400_is_space_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'is_space_menu', $this->tinyInteger(1)->notNull()->defaultValue(0)->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('wiki_page', 'is_space_menu');
    }
}
