<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%goods_activity}}".
 *
 * @property integer $act_id
 * @property string $act_name
 * @property string $act_desc
 * @property integer $act_type
 * @property integer $goods_id
 * @property integer $product_id
 * @property string $goods_name
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $is_finished
 * @property string $ext_info
 */
class GoodsActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_activity}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['act_name', 'act_desc', 'act_type', 'goods_id', 'goods_name', 'start_time', 'end_time', 'is_finished', 'ext_info'], 'required'],
            [['act_desc', 'ext_info'], 'string'],
            [['act_type', 'goods_id', 'product_id', 'start_time', 'end_time', 'is_finished'], 'integer'],
            [['act_name', 'goods_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'act_id' => 'Act ID',
            'act_name' => 'Act Name',
            'act_desc' => 'Act Desc',
            'act_type' => 'Act Type',
            'goods_id' => 'Goods ID',
            'product_id' => 'Product ID',
            'goods_name' => 'Goods Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'is_finished' => 'Is Finished',
            'ext_info' => 'Ext Info',
        ];
    }
}
