<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%virtual_card}}".
 *
 * @property integer $card_id
 * @property integer $goods_id
 * @property string $card_sn
 * @property string $card_password
 * @property integer $add_date
 * @property integer $end_date
 * @property integer $is_saled
 * @property string $order_sn
 * @property string $crc32
 */
class VirtualCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%virtual_card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'add_date', 'end_date', 'is_saled'], 'integer'],
            [['card_sn', 'card_password'], 'string', 'max' => 60],
            [['order_sn'], 'string', 'max' => 20],
            [['crc32'], 'string', 'max' => 12],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'card_id' => 'Card ID',
            'goods_id' => 'Goods ID',
            'card_sn' => 'Card Sn',
            'card_password' => 'Card Password',
            'add_date' => 'Add Date',
            'end_date' => 'End Date',
            'is_saled' => 'Is Saled',
            'order_sn' => 'Order Sn',
            'crc32' => 'Crc32',
        ];
    }
}
