<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%vote}}".
 *
 * @property integer $vote_id
 * @property string $vote_name
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $can_multi
 * @property integer $vote_count
 */
class Vote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'can_multi', 'vote_count'], 'integer'],
            [['vote_name'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vote_id' => 'Vote ID',
            'vote_name' => 'Vote Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'can_multi' => 'Can Multi',
            'vote_count' => 'Vote Count',
        ];
    }
}
