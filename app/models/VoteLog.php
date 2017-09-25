<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%vote_log}}".
 *
 * @property integer $log_id
 * @property integer $vote_id
 * @property string $ip_address
 * @property integer $vote_time
 */
class VoteLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vote_id', 'vote_time'], 'integer'],
            [['ip_address'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'vote_id' => 'Vote ID',
            'ip_address' => 'Ip Address',
            'vote_time' => 'Vote Time',
        ];
    }
}
