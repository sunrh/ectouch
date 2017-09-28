<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%delivery_goods}}".
 *
 * @property integer $rec_id
 * @property integer $delivery_id
 * @property integer $goods_id
 * @property integer $product_id
 * @property string $product_sn
 * @property string $goods_name
 * @property string $brand_name
 * @property string $goods_sn
 * @property integer $is_real
 * @property string $extension_code
 * @property integer $parent_id
 * @property integer $send_number
 * @property string $goods_attr
 */
class DeliveryGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%delivery_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_id', 'goods_id', 'product_id', 'is_real', 'parent_id', 'send_number'], 'integer'],
            [['goods_attr'], 'string'],
            [['product_sn', 'brand_name', 'goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['extension_code'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'delivery_id' => 'Delivery ID',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'product_sn' => 'Product Sn',
            'goods_name' => 'Goods Name',
            'brand_name' => 'Brand Name',
            'goods_sn' => 'Goods Sn',
            'is_real' => 'Is Real',
            'extension_code' => 'Extension Code',
            'parent_id' => 'Parent ID',
            'send_number' => 'Send Number',
            'goods_attr' => 'Goods Attr',
        ];
    }
}
