<?php

use yii\db\Migration;

class m170908_070341_create_table_package_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%package_goods}}', [
            'package_id' => $this->integer(8)->unsigned()->notNull(),
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'product_id' => $this->integer(8)->unsigned()->notNull(),
            'goods_number' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('1'),
            'admin_id' => $this->smallInteger(3)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%package_goods}}', ['package_id','goods_id','product_id','admin_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%package_goods}}');
    }
}
