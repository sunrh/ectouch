<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%affiliate_log}}".
 *
 * @property integer $log_id
 * @property integer $order_id
 * @property integer $time
 * @property integer $user_id
 * @property string $user_name
 * @property string $money
 * @property integer $point
 * @property integer $separate_type
 */
class AffiliateLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%affiliate_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'time', 'user_id'], 'required'],
            [['order_id', 'time', 'user_id', 'point', 'separate_type'], 'integer'],
            [['money'], 'number'],
            [['user_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'order_id' => 'Order ID',
            'time' => 'Time',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'money' => 'Money',
            'point' => 'Point',
            'separate_type' => 'Separate Type',
        ];
    }
}
