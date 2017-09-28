<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%group_goods}}".
 *
 * @property integer $parent_id
 * @property integer $goods_id
 * @property string $goods_price
 * @property integer $admin_id
 */
class GroupGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'goods_id', 'admin_id'], 'required'],
            [['parent_id', 'goods_id', 'admin_id'], 'integer'],
            [['goods_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent_id' => 'Parent ID',
            'goods_id' => 'Goods ID',
            'goods_price' => 'Goods Price',
            'admin_id' => 'Admin ID',
        ];
    }
}
