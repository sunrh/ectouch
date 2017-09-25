<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%link_goods}}".
 *
 * @property integer $goods_id
 * @property integer $link_goods_id
 * @property integer $is_double
 * @property integer $admin_id
 */
class LinkGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%link_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'link_goods_id', 'admin_id'], 'required'],
            [['goods_id', 'link_goods_id', 'is_double', 'admin_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'link_goods_id' => 'Link Goods ID',
            'is_double' => 'Is Double',
            'admin_id' => 'Admin ID',
        ];
    }
}
