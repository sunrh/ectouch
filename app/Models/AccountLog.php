<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%account_log}}".
 *
 * @property integer $log_id
 * @property integer $user_id
 * @property string $user_money
 * @property string $frozen_money
 * @property integer $rank_points
 * @property integer $pay_points
 * @property integer $change_time
 * @property string $change_desc
 * @property integer $change_type
 */
class AccountLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_money', 'frozen_money', 'rank_points', 'pay_points', 'change_time', 'change_desc', 'change_type'], 'required'],
            [['user_id', 'rank_points', 'pay_points', 'change_time', 'change_type'], 'integer'],
            [['user_money', 'frozen_money'], 'number'],
            [['change_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'user_id' => 'User ID',
            'user_money' => 'User Money',
            'frozen_money' => 'Frozen Money',
            'rank_points' => 'Rank Points',
            'pay_points' => 'Pay Points',
            'change_time' => 'Change Time',
            'change_desc' => 'Change Desc',
            'change_type' => 'Change Type',
        ];
    }
}
