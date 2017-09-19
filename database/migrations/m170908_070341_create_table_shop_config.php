<?php

use yii\db\Migration;

class m170908_070341_create_table_shop_config extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%shop_config}}', [
            'id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'parent_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'code' => $this->string(30)->notNull()->defaultValue(''),
            'type' => $this->string(10)->notNull()->defaultValue(''),
            'store_range' => $this->string(255)->notNull()->defaultValue(''),
            'store_dir' => $this->string(255)->notNull()->defaultValue(''),
            'value' => $this->text()->notNull(),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('1'),
        ], $tableOptions);

        $this->createIndex('code', '{{%shop_config}}', 'code', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%shop_config}}');
    }
}
