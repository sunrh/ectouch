<?php

use yii\db\Migration;

class m170908_070341_create_table_goods extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods}}', [
            'goods_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'cat_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'goods_sn' => $this->string(60)->notNull()->defaultValue(''),
            'goods_name' => $this->string(120)->notNull()->defaultValue(''),
            'goods_name_style' => $this->string(60)->notNull()->defaultValue('+'),
            'click_count' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'brand_id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'provider_name' => $this->string(100)->notNull()->defaultValue(''),
            'goods_number' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'goods_weight' => $this->decimal(10,3)->unsigned()->notNull()->defaultValue('0.000'),
            'market_price' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
            'shop_price' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
            'promote_price' => $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00'),
            'promote_start_date' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'promote_end_date' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'warn_number' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('1'),
            'keywords' => $this->string(255)->notNull()->defaultValue(''),
            'goods_brief' => $this->string(255)->notNull()->defaultValue(''),
            'goods_desc' => $this->text()->notNull(),
            'goods_thumb' => $this->string(255)->notNull()->defaultValue(''),
            'goods_img' => $this->string(255)->notNull()->defaultValue(''),
            'original_img' => $this->string(255)->notNull()->defaultValue(''),
            'is_real' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('1'),
            'extension_code' => $this->string(30)->notNull()->defaultValue(''),
            'is_on_sale' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'is_alone_sale' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'is_shipping' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'integral' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'add_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'sort_order' => $this->smallInteger(4)->unsigned()->notNull()->defaultValue('100'),
            'is_delete' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_best' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_new' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_hot' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'is_promote' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'bonus_type_id' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'last_update' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'goods_type' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'seller_note' => $this->string(255)->notNull()->defaultValue(''),
            'give_integral' => $this->integer(11)->notNull()->defaultValue('-1'),
            'rank_integral' => $this->integer(11)->notNull()->defaultValue('-1'),
            'suppliers_id' => $this->smallInteger(5)->unsigned(),
            'is_check' => $this->smallInteger(1)->unsigned(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods}}');
    }
}
