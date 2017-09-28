<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%sessions}}".
 *
 * @property string $sesskey
 * @property integer $expiry
 * @property integer $userid
 * @property integer $adminid
 * @property string $ip
 * @property string $user_name
 * @property integer $user_rank
 * @property string $discount
 * @property string $email
 * @property string $data
 */
class Sessions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sessions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sesskey', 'user_name', 'user_rank', 'discount', 'email'], 'required'],
            [['expiry', 'userid', 'adminid', 'user_rank'], 'integer'],
            [['discount'], 'number'],
            [['sesskey'], 'string', 'max' => 32],
            [['ip'], 'string', 'max' => 15],
            [['user_name', 'email'], 'string', 'max' => 60],
            [['data'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sesskey' => 'Sesskey',
            'expiry' => 'Expiry',
            'userid' => 'Userid',
            'adminid' => 'Adminid',
            'ip' => 'Ip',
            'user_name' => 'User Name',
            'user_rank' => 'User Rank',
            'discount' => 'Discount',
            'email' => 'Email',
            'data' => 'Data',
        ];
    }
}
