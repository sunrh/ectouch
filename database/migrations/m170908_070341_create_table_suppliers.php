<?php

use yii\db\Migration;

class m170908_070341_create_table_suppliers extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%suppliers}}', [
            'suppliers_id' => $this->smallInteger(5)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'suppliers_name' => $this->string(255),
            'suppliers_desc' => $this->text(),
            'is_check' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%suppliers}}');
    }
}
