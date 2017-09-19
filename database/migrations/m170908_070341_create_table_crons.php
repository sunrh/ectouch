<?php

use yii\db\Migration;

class m170908_070341_create_table_crons extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%crons}}', [
            'cron_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cron_code' => $this->string(20)->notNull(),
            'cron_name' => $this->string(120)->notNull(),
            'cron_desc' => $this->text(),
            'cron_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'cron_config' => $this->text()->notNull(),
            'thistime' => $this->integer(10)->notNull()->defaultValue('0'),
            'nextime' => $this->integer(10)->notNull(),
            'day' => $this->smallInteger(2)->notNull(),
            'week' => $this->string(1)->notNull(),
            'hour' => $this->string(2)->notNull(),
            'minute' => $this->string(255)->notNull(),
            'enable' => $this->smallInteger(1)->notNull()->defaultValue('1'),
            'run_once' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'allow_ip' => $this->string(100)->notNull()->defaultValue(''),
            'alow_files' => $this->string(255)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%crons}}');
    }
}
