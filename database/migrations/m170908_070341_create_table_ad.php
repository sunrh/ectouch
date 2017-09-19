<?php

use yii\db\Migration;

class m170908_070341_create_table_ad extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ad}}', [
            'ad_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'position_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'media_type' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'ad_name' => $this->string(60)->notNull()->defaultValue(''),
            'ad_link' => $this->string(255)->notNull()->defaultValue(''),
            'ad_code' => $this->text()->notNull(),
            'start_time' => $this->integer(11)->notNull()->defaultValue('0'),
            'end_time' => $this->integer(11)->notNull()->defaultValue('0'),
            'link_man' => $this->string(60)->notNull()->defaultValue(''),
            'link_email' => $this->string(60)->notNull()->defaultValue(''),
            'link_phone' => $this->string(60)->notNull()->defaultValue(''),
            'click_count' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'enabled' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('1'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ad}}');
    }
}
