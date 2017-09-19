<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%snatch_log}}".
 *
 * @property integer $log_id
 * @property integer $snatch_id
 * @property integer $user_id
 * @property string $bid_price
 * @property integer $bid_time
 */
class SnatchLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%snatch_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['snatch_id', 'user_id', 'bid_time'], 'integer'],
            [['bid_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'snatch_id' => 'Snatch ID',
            'user_id' => 'User ID',
            'bid_price' => 'Bid Price',
            'bid_time' => 'Bid Time',
        ];
    }
}
