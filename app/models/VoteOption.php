<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%vote_option}}".
 *
 * @property integer $option_id
 * @property integer $vote_id
 * @property string $option_name
 * @property integer $option_count
 * @property integer $option_order
 */
class VoteOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vote_id', 'option_count', 'option_order'], 'integer'],
            [['option_name'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'option_id' => 'Option ID',
            'vote_id' => 'Vote ID',
            'option_name' => 'Option Name',
            'option_count' => 'Option Count',
            'option_order' => 'Option Order',
        ];
    }
}
