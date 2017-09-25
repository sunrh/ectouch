<?php

use yii\db\Migration;

class m170908_070341_create_table_template extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%template}}', [
            'filename' => $this->string(30)->notNull()->defaultValue(''),
            'region' => $this->string(40)->notNull()->defaultValue(''),
            'library' => $this->string(40)->notNull()->defaultValue(''),
            'sort_order' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'id' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('0'),
            'number' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('5'),
            'type' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'theme' => $this->string(60)->notNull()->defaultValue(''),
            'remarks' => $this->string(30)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%template}}');
    }
}
