<?php

use yii\db\Migration;

class m170908_070341_create_table_adsense extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%adsense}}', [
            'from_ad' => $this->smallInteger(5)->notNull()->defaultValue('0'),
            'referer' => $this->string(255)->notNull()->defaultValue(''),
            'clicks' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%adsense}}');
    }
}
