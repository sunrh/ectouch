<?php

use yii\db\Migration;

class m170908_070341_create_table_auction_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%auction_log}}', [
            'log_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'act_id' => $this->integer(8)->unsigned()->notNull(),
            'bid_user' => $this->integer(8)->unsigned()->notNull(),
            'bid_price' => $this->decimal(10,2)->unsigned()->notNull(),
            'bid_time' => $this->integer(10)->unsigned()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auction_log}}');
    }
}
