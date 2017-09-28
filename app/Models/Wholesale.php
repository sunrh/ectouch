<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%wholesale}}".
 *
 * @property integer $act_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $rank_ids
 * @property string $prices
 * @property integer $enabled
 */
class Wholesale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wholesale}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'goods_name', 'rank_ids', 'prices', 'enabled'], 'required'],
            [['goods_id', 'enabled'], 'integer'],
            [['prices'], 'string'],
            [['goods_name', 'rank_ids'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'act_id' => 'Act ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'rank_ids' => 'Rank Ids',
            'prices' => 'Prices',
            'enabled' => 'Enabled',
        ];
    }
}
