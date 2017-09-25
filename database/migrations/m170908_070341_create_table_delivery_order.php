<?php

use yii\db\Migration;

class m170908_070341_create_table_delivery_order extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%delivery_order}}', [
            'delivery_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'delivery_sn' => $this->string(20)->notNull(),
            'order_sn' => $this->string(20)->notNull(),
            'order_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'invoice_no' => $this->string(50),
            'add_time' => $this->integer(10)->unsigned()->defaultValue('0'),
            'shipping_id' => $this->smallInteger(3)->unsigned()->defaultValue('0'),
            'shipping_name' => $this->string(120),
            'user_id' => $this->integer(8)->unsigned()->defaultValue('0'),
            'action_user' => $this->string(30),
            'consignee' => $this->string(60),
            'address' => $this->string(250),
            'country' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
            'province' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
            'city' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
            'district' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
            'sign_building' => $this->string(120),
            'email' => $this->string(60),
            'zipcode' => $this->string(60),
            'tel' => $this->string(60),
            'mobile' => $this->string(60),
            'best_time' => $this->string(120),
            'postscript' => $this->string(255),
            'how_oos' => $this->string(120),
            'insure_fee' => $this->decimal(10,2)->unsigned()->defaultValue('0.00'),
            'shipping_fee' => $this->decimal(10,2)->unsigned()->defaultValue('0.00'),
            'update_time' => $this->integer(10)->unsigned()->defaultValue('0'),
            'suppliers_id' => $this->smallInteger(5)->defaultValue('0'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'agency_id' => $this->smallInteger(5)->unsigned()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%delivery_order}}');
    }
}
