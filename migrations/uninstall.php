<?php

class uninstall extends ZDbMigration {

    public function up() {

        $this->dropTable('wiki_page');
        $this->dropTable('wiki_page_revision');
    }

    public function down() {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}