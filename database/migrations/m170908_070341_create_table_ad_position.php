<?php

use yii\db\Migration;

class m170908_070341_create_table_ad_position extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%ad_position}}', [
            'position_id' => $this->smallInteger(3)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'position_name' => $this->string(60)->notNull()->defaultValue(''),
            'ad_width' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'ad_height' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'position_desc' => $this->string(255)->notNull()->defaultValue(''),
            'position_style' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ad_position}}');
    }
}
