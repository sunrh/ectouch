<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%collect_goods}}".
 *
 * @property integer $rec_id
 * @property integer $user_id
 * @property integer $goods_id
 * @property integer $add_time
 * @property integer $is_attention
 */
class CollectGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collect_goods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'goods_id', 'add_time', 'is_attention'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'user_id' => 'User ID',
            'goods_id' => 'Goods ID',
            'add_time' => 'Add Time',
            'is_attention' => 'Is Attention',
        ];
    }
}
