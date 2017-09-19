<?php

use yii\db\Migration;

class m170908_070342_create_table_user_feed extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_feed}}', [
            'feed_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'value_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'feed_type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_feed' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_feed}}');
    }
}
