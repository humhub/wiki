<?php

use yii\db\Schema;
use yii\db\Migration;

class m141004_022835_initial extends Migration
{

    public function up()
    {
        $this->createTable('wiki_page', array(
            'id' => 'pk',
            'title' => 'varchar(255) NOT NULL',
            'is_home' => 'tinyint(4) NOT NULL DEFAULT 0',
            'admin_only' => 'tinyint(4) NOT NULL DEFAULT 0',
                ), '');

        $this->createTable('wiki_page_revision', array(
            'id' => 'pk',
            'revision' => 'int(11) NOT NULL',
            'is_latest' => 'tinyint(1) NOT NULL DEFAULT 0',
            'wiki_page_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'content' => 'TEXT NULL',
                ), '');
    }

    public function down()
    {
        echo "m141004_022835_initial does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
