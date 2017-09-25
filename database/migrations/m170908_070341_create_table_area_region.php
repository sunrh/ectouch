<?php

use yii\db\Migration;

class m170908_070341_create_table_area_region extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%area_region}}', [
            'shipping_area_id' => $this->smallInteger(5)->unsigned()->notNull(),
            'region_id' => $this->smallInteger(5)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%area_region}}', ['shipping_area_id','region_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%area_region}}');
    }
}
