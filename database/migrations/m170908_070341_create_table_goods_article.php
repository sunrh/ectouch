<?php

use yii\db\Migration;

class m170908_070341_create_table_goods_article extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%goods_article}}', [
            'goods_id' => $this->integer(8)->unsigned()->notNull(),
            'article_id' => $this->integer(8)->unsigned()->notNull(),
            'admin_id' => $this->smallInteger(3)->unsigned()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('primary_key', '{{%goods_article}}', ['goods_id','article_id','admin_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%goods_article}}');
    }
}
