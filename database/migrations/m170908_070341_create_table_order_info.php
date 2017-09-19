<?php

use yii\db\Migration;

class m170908_070341_create_table_order_info extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order_info}}', [
            'order_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'order_sn' => $this->string(20)->notNull()->defaultValue(''),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'order_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'shipping_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'pay_status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'consignee' => $this->string(60)->notNull()->defaultValue(''),
            'country' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'province' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'city' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'district' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'address' => $this->string(255)->notNull()->defaultValue(''),
            'zipcode' => $this->string(60)->notNull()->defaultValue(''),
            'tel' => $this->string(60)->notNull()->defaultValue(''),
            'mobile' => $this->string(60)->notNull()->defaultValue(''),
            'email' => $this->string(60)->notNull()->defaultValue(''),
            'best_time' => $this->string(120)->notNull()->defaultValue(''),
            'sign_building' => $this->string(120)->notNull()->defaultValue(''),
            'postscript' => $this->string(255)->notNull()->defaultValue(''),
            'shipping_id' => $this->smallInteger(3)->notNull()->defaultValue('0'),
            'shipping_name' => $this->string(120)->notNull()->defaultValue(''),
            'pay_id' => $this->smallInteger(3)->notNull()->defaultValue('0'),
            'pay_name' => $this->string(120)->notNull()->defaultValue(''),
            'how_oos' => $this->string(120)->notNull()->defaultValue(''),
            'how_surplus' => $this->string(120)->notNull()->defaultValue(''),
            'pack_name' => $this->string(120)->notNull()->defaultValue(''),
            'card_name' => $this->string(120)->notNull()->defaultValue(''),
            'card_message' => $this->string(255)->notNull()->defaultValue(''),
            'inv_payee' => $this->string(120)->notNull()->defaultValue(''),
            'inv_content' => $this->string(120)->notNull()->defaultValue(''),
            'goods_amount' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'shipping_fee' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'insure_fee' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'pay_fee' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'pack_fee' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'card_fee' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'money_paid' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'surplus' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'integral' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'integral_money' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'bonus' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'order_amount' => $this->decimal(10,2)->notNull()->defaultValue('0.00'),
            'from_ad' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'referer' => $this->string(255)->notNull()->defaultValue(''),
            'add_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'confirm_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'pay_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'shipping_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'pack_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'card_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'bonus_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'invoice_no' => $this->string(255)->notNull()->defaultValue(''),
            'extension_code' => $this->string(30)->notNull()->defaultValue(''),
            'extension_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'to_buyer' => $this->string(255)->notNull()->defaultValue(''),
            'pay_note' => $this->string(255)->notNull()->defaultValue(''),
            'agency_id' => $this->smallInteger(5)->unsigned()->notNull(),
            'inv_type' => $this->string(60)->notNull(),
            'tax' => $this->decimal(10,2)->notNull(),
            'is_separate' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'parent_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'discount' => $this->decimal(10,2)->notNull(),
        ], $tableOptions);

        $this->createIndex('order_sn', '{{%order_info}}', 'order_sn', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%order_info}}');
    }
}
