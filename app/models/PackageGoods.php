<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%package_goods}}".
 *
 * @property integer $package_id
 * @property integer $goods_id
 * @property integer $product_id
 * @property integer $goods_number
 * @property integer $admin_id
 */
class PackageGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%package_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['package_id', 'goods_id', 'product_id', 'admin_id'], 'required'],
            [['package_id', 'goods_id', 'product_id', 'goods_number', 'admin_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'package_id' => 'Package ID',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'goods_number' => 'Goods Number',
            'admin_id' => 'Admin ID',
        ];
    }
}
