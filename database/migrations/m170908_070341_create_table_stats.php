<?php

use yii\db\Migration;

class m170908_070341_create_table_stats extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%stats}}', [
            'access_time' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'ip_address' => $this->string(15)->notNull()->defaultValue(''),
            'visit_times' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue('1'),
            'browser' => $this->string(60)->notNull()->defaultValue(''),
            'system' => $this->string(20)->notNull()->defaultValue(''),
            'language' => $this->string(20)->notNull()->defaultValue(''),
            'area' => $this->string(30)->notNull()->defaultValue(''),
            'referer_domain' => $this->string(100)->notNull()->defaultValue(''),
            'referer_path' => $this->string(200)->notNull()->defaultValue(''),
            'access_url' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%stats}}');
    }
}
