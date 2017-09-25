<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%back_goods}}".
 *
 * @property integer $rec_id
 * @property integer $back_id
 * @property integer $goods_id
 * @property integer $product_id
 * @property string $product_sn
 * @property string $goods_name
 * @property string $brand_name
 * @property string $goods_sn
 * @property integer $is_real
 * @property integer $send_number
 * @property string $goods_attr
 */
class BackGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%back_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['back_id', 'goods_id', 'product_id', 'is_real', 'send_number'], 'integer'],
            [['goods_attr'], 'string'],
            [['product_sn', 'brand_name', 'goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'back_id' => 'Back ID',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'product_sn' => 'Product Sn',
            'goods_name' => 'Goods Name',
            'brand_name' => 'Brand Name',
            'goods_sn' => 'Goods Sn',
            'is_real' => 'Is Real',
            'send_number' => 'Send Number',
            'goods_attr' => 'Goods Attr',
        ];
    }
}
