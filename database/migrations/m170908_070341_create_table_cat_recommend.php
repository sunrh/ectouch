<?php

use yii\db\Migration;

class m170908_070341_create_table_cat_recommend extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cat_recommend}}', [
            'cat_id' => $this->smallInteger(5)->notNull(),
            'recommend_type' => $this->smallInteger(1)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%cat_recommend}}', ['cat_id','recommend_type']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%cat_recommend}}');
    }
}
