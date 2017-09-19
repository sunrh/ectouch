<?php

use yii\db\Migration;

class m170908_070341_create_table_link_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%link_goods}}', [
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'link_goods_id' => $this->integer(8)->unsigned()->notNull(),
            'is_double' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'admin_id' => $this->smallInteger(3)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%link_goods}}', ['goods_id','link_goods_id','admin_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%link_goods}}');
    }
}
