<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%exchange_goods}}".
 *
 * @property integer $goods_id
 * @property integer $exchange_integral
 * @property integer $is_exchange
 * @property integer $is_hot
 */
class ExchangeGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%exchange_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'required'],
            [['goods_id', 'exchange_integral', 'is_exchange', 'is_hot'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'exchange_integral' => 'Exchange Integral',
            'is_exchange' => 'Is Exchange',
            'is_hot' => 'Is Hot',
        ];
    }
}
