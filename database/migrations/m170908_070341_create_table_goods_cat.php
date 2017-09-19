<?php

use yii\db\Migration;

class m170908_070341_create_table_goods_cat extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods_cat}}', [
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'cat_id' => $this->smallInteger(5)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%goods_cat}}', ['goods_id','cat_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods_cat}}');
    }
}
