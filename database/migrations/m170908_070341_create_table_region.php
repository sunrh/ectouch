<?php

use yii\db\Migration;

class m170908_070341_create_table_region extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%region}}', [
            'region_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'parent_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'region_name' => $this->string(120)->notNull()->defaultValue(''),
            'region_type' => $this->smallInteger(1)->notNull()->defaultValue('2'),
            'agency_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%region}}');
    }
}
