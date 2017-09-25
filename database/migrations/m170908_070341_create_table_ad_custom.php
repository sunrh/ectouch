<?php

use yii\db\Migration;

class m170908_070341_create_table_ad_custom extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ad_custom}}', [
            'ad_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'ad_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'ad_name' => $this->string(60),
            'add_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'content' => $this->text(),
            'url' => $this->string(255),
            'ad_status' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ad_custom}}');
    }
}
