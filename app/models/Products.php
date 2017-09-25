<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property integer $product_id
 * @property integer $goods_id
 * @property string $goods_attr
 * @property string $product_sn
 * @property integer $product_number
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'product_number'], 'integer'],
            [['goods_attr'], 'string', 'max' => 50],
            [['product_sn'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'goods_id' => 'Goods ID',
            'goods_attr' => 'Goods Attr',
            'product_sn' => 'Product Sn',
            'product_number' => 'Product Number',
        ];
    }
}
