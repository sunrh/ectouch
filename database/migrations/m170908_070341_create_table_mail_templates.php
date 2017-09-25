<?php

use yii\db\Migration;

class m170908_070341_create_table_mail_templates extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%mail_templates}}', [
            'template_id' => $this->smallInteger(1)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'template_code' => $this->string(30)->notNull()->defaultValue(''),
            'is_html' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue('0'),
            'template_subject' => $this->string(200)->notNull()->defaultValue(''),
            'template_content' => $this->text()->notNull(),
            'last_modify' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'last_send' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'type' => $this->string(10)->notNull(),
        ], $tableOptions);

        $this->createIndex('template_code', '{{%mail_templates}}', 'template_code', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%mail_templates}}');
    }
}
