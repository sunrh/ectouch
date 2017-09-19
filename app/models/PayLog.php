<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pay_log}}".
 *
 * @property integer $log_id
 * @property integer $order_id
 * @property string $order_amount
 * @property integer $order_type
 * @property integer $is_paid
 */
class PayLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pay_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'order_type', 'is_paid'], 'integer'],
            [['order_amount'], 'required'],
            [['order_amount'], 'number'],
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
            'order_amount' => 'Order Amount',
            'order_type' => 'Order Type',
            'is_paid' => 'Is Paid',
        ];
    }
}
