<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%booking_goods}}".
 *
 * @property integer $rec_id
 * @property integer $user_id
 * @property string $email
 * @property string $link_man
 * @property string $tel
 * @property integer $goods_id
 * @property string $goods_desc
 * @property integer $goods_number
 * @property integer $booking_time
 * @property integer $is_dispose
 * @property string $dispose_user
 * @property integer $dispose_time
 * @property string $dispose_note
 */
class BookingGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%booking_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'goods_id', 'goods_number', 'booking_time', 'is_dispose', 'dispose_time'], 'integer'],
            [['email', 'link_man', 'tel'], 'string', 'max' => 60],
            [['goods_desc', 'dispose_note'], 'string', 'max' => 255],
            [['dispose_user'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'user_id' => 'User ID',
            'email' => 'Email',
            'link_man' => 'Link Man',
            'tel' => 'Tel',
            'goods_id' => 'Goods ID',
            'goods_desc' => 'Goods Desc',
            'goods_number' => 'Goods Number',
            'booking_time' => 'Booking Time',
            'is_dispose' => 'Is Dispose',
            'dispose_user' => 'Dispose User',
            'dispose_time' => 'Dispose Time',
            'dispose_note' => 'Dispose Note',
        ];
    }
}
