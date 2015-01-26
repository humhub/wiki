<?php

class uninstall extends ZDbMigration {

    public function up() {

        $this->dropTable('task');
        $this->dropTable('task_user');
    }

    public function down() {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}