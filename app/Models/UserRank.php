<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%user_rank}}".
 *
 * @property integer $rank_id
 * @property string $rank_name
 * @property integer $min_points
 * @property integer $max_points
 * @property integer $discount
 * @property integer $show_price
 * @property integer $special_rank
 */
class UserRank extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_rank}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['min_points', 'max_points', 'discount', 'show_price', 'special_rank'], 'integer'],
            [['rank_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rank_id' => 'Rank ID',
            'rank_name' => 'Rank Name',
            'min_points' => 'Min Points',
            'max_points' => 'Max Points',
            'discount' => 'Discount',
            'show_price' => 'Show Price',
            'special_rank' => 'Special Rank',
        ];
    }
}
