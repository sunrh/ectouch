<?php

use yii\db\Migration;

class m170908_070341_create_table_plugins extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%plugins}}', [
            'code' => $this->string(30)->notNull()->append('PRIMARY KEY'),
            'version' => $this->string(10)->notNull()->defaultValue(''),
            'library' => $this->string(255)->notNull()->defaultValue(''),
            'assign' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'install_date' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%plugins}}');
    }
}
