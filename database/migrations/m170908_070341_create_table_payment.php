<?php

use yii\db\Migration;

class m170908_070341_create_table_payment extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%payment}}', [
            'pay_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'pay_code' => $this->string(20)->notNull()->defaultValue(''),
            'pay_name' => $this->string(120)->notNull()->defaultValue(''),
            'pay_fee' => $this->string(10)->notNull()->defaultValue('0'),
            'pay_desc' => $this->text()->notNull(),
            'pay_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'pay_config' => $this->text()->notNull(),
            'enabled' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_cod' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_online' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);

        $this->createIndex('pay_code', '{{%payment}}', 'pay_code', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%payment}}');
    }
}
