<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%reg_extend_info}}".
 *
 * @property integer $Id
 * @property integer $user_id
 * @property integer $reg_field_id
 * @property string $content
 */
class RegExtendInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reg_extend_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'reg_field_id', 'content'], 'required'],
            [['user_id', 'reg_field_id'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'user_id' => 'User ID',
            'reg_field_id' => 'Reg Field ID',
            'content' => 'Content',
        ];
    }
}
