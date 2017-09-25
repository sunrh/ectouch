<?php

use yii\db\Migration;

class m170908_070341_create_table_favourable_activity extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%favourable_activity}}', [
            'act_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'act_name' => $this->string(255)->notNull(),
            'start_time' => $this->integer(10)->unsigned()->notNull(),
            'end_time' => $this->integer(10)->unsigned()->notNull(),
            'user_rank' => $this->string(255)->notNull(),
            'act_range' => $this->smallInteger(3)->unsigned()->notNull(),
            'act_range_ext' => $this->string(255)->notNull(),
            'min_amount' => $this->decimal(10,2)->unsigned()->notNull(),
            'max_amount' => $this->decimal(10,2)->unsigned()->notNull(),
            'act_type' => $this->smallInteger(3)->unsigned()->notNull(),
            'act_type_ext' => $this->decimal(10,2)->unsigned()->notNull(),
            'gift' => $this->text()->notNull(),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('50'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%favourable_activity}}');
    }
}
