<?php

use yii\db\Migration;

class m170908_070341_create_table_admin_action extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin_action}}', [
            'action_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'parent_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'action_code' => $this->string(20)->notNull()->defaultValue(''),
            'relevance' => $this->string(20)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%admin_action}}');
    }
}
