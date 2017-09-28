<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%email_list}}".
 *
 * @property integer $id
 * @property string $email
 * @property integer $stat
 * @property string $hash
 */
class EmailList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%email_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'hash'], 'required'],
            [['stat'], 'integer'],
            [['email'], 'string', 'max' => 60],
            [['hash'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'stat' => 'Stat',
            'hash' => 'Hash',
        ];
    }
}
