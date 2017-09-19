<?php

use yii\db\Migration;

class m170908_070342_create_table_volume_price extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%volume_price}}', [
            'price_type' => $this->smallInteger(1)->unsigned()->notNull(),
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'volume_number' => $this->smallInteger(5)->unsigned()->notNull(),
            'volume_price' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%volume_price}}', ['price_type','goods_id','volume_number']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%volume_price}}');
    }
}
