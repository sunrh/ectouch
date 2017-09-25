<?php

use yii\db\Migration;

class m170908_070341_create_table_group_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%group_goods}}', [
            'parent_id' => $this->integer(8)->unsigned()->notNull(),
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'goods_price' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
            'admin_id' => $this->smallInteger(3)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%group_goods}}', ['parent_id','goods_id','admin_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%group_goods}}');
    }
}
