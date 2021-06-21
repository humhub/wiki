<?php

use yii\db\Migration;

/**
 * Class m210618_131400_is_container_menu
 */
class m210618_131400_is_container_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('wiki_page', 'is_container_menu', $this->tinyInteger(1)->notNull()->defaultValue(0)->unsigned());
        $this->addColumn('wiki_page', 'container_menu_order', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('wiki_page', 'is_container_menu');
        $this->dropColumn('wiki_page', 'container_menu_order');
    }
}
