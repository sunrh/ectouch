<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%auction_log}}".
 *
 * @property integer $log_id
 * @property integer $act_id
 * @property integer $bid_user
 * @property string $bid_price
 * @property integer $bid_time
 */
class AuctionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auction_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['act_id', 'bid_user', 'bid_price', 'bid_time'], 'required'],
            [['act_id', 'bid_user', 'bid_time'], 'integer'],
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
            'act_id' => 'Act ID',
            'bid_user' => 'Bid User',
            'bid_price' => 'Bid Price',
            'bid_time' => 'Bid Time',
        ];
    }
}
