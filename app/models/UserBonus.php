<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_bonus}}".
 *
 * @property integer $bonus_id
 * @property integer $bonus_type_id
 * @property string $bonus_sn
 * @property integer $user_id
 * @property integer $used_time
 * @property integer $order_id
 * @property integer $emailed
 */
class UserBonus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_bonus}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bonus_type_id', 'bonus_sn', 'user_id', 'used_time', 'order_id', 'emailed'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bonus_id' => 'Bonus ID',
            'bonus_type_id' => 'Bonus Type ID',
            'bonus_sn' => 'Bonus Sn',
            'user_id' => 'User ID',
            'used_time' => 'Used Time',
            'order_id' => 'Order ID',
            'emailed' => 'Emailed',
        ];
    }
}
