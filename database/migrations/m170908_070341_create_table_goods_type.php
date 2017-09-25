<?php

use yii\db\Migration;

class m170908_070341_create_table_goods_type extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods_type}}', [
            'cat_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cat_name' => $this->string(60)->notNull()->defaultValue(''),
            'enabled' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'attr_group' => $this->string(255)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods_type}}');
    }
}
