<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sessions_data}}".
 *
 * @property string $sesskey
 * @property integer $expiry
 * @property string $data
 */
class SessionsData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sessions_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sesskey', 'data'], 'required'],
            [['expiry'], 'integer'],
            [['data'], 'string'],
            [['sesskey'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sesskey' => 'Sesskey',
            'expiry' => 'Expiry',
            'data' => 'Data',
        ];
    }
}
