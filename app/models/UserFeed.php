<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_feed}}".
 *
 * @property integer $feed_id
 * @property integer $user_id
 * @property integer $value_id
 * @property integer $goods_id
 * @property integer $feed_type
 * @property integer $is_feed
 */
class UserFeed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_feed}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'value_id', 'goods_id', 'feed_type', 'is_feed'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'feed_id' => 'Feed ID',
            'user_id' => 'User ID',
            'value_id' => 'Value ID',
            'goods_id' => 'Goods ID',
            'feed_type' => 'Feed Type',
            'is_feed' => 'Is Feed',
        ];
    }
}
