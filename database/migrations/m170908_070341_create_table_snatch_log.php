<?php

use yii\db\Migration;

class m170908_070341_create_table_snatch_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%snatch_log}}', [
            'log_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'snatch_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'bid_price' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'bid_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%snatch_log}}');
    }
}
