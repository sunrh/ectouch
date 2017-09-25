<?php

use yii\db\Migration;

class m170908_070342_create_table_wholesale extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%wholesale}}', [
            'act_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'goods_name' => $this->string(255)->notNull(),
            'rank_ids' => $this->string(255)->notNull(),
            'prices' => $this->text()->notNull(),
            'enabled' => $this->smallInteger(3)->unsigned()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%wholesale}}');
    }
}
