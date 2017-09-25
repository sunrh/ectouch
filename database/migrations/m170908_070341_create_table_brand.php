<?php

use yii\db\Migration;

class m170908_070341_create_table_brand extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%brand}}', [
            'brand_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'brand_name' => $this->string(60)->notNull()->defaultValue(''),
            'brand_logo' => $this->string(80)->notNull()->defaultValue(''),
            'brand_desc' => $this->text()->notNull(),
            'site_url' => $this->string(255)->notNull()->defaultValue(''),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('50'),
            'is_show' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%brand}}');
    }
}
