<?php

use yii\db\Migration;

class m170908_070342_create_table_user_rank extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_rank}}', [
            'rank_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'rank_name' => $this->string(30)->notNull()->defaultValue(''),
            'min_points' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'max_points' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'discount' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue('0'),
            'show_price' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('1'),
            'special_rank' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_rank}}');
    }
}
