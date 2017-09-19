<?php

use yii\db\Migration;

class m170908_070342_create_table_user_address extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_address}}', [
            'address_id' => $this->integer(8)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'address_name' => $this->string(50)->notNull()->defaultValue(''),
            'user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue('0'),
            'consignee' => $this->string(60)->notNull()->defaultValue(''),
            'email' => $this->string(60)->notNull()->defaultValue(''),
            'country' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'province' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'city' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'district' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'address' => $this->string(120)->notNull()->defaultValue(''),
            'zipcode' => $this->string(60)->notNull()->defaultValue(''),
            'tel' => $this->string(60)->notNull()->defaultValue(''),
            'mobile' => $this->string(60)->notNull()->defaultValue(''),
            'sign_building' => $this->string(120)->notNull()->defaultValue(''),
            'best_time' => $this->string(120)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_address}}');
    }
}
