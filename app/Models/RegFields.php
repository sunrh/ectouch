<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%reg_fields}}".
 *
 * @property integer $id
 * @property string $reg_field_name
 * @property integer $dis_order
 * @property integer $display
 * @property integer $type
 * @property integer $is_need
 */
class RegFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reg_fields}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reg_field_name'], 'required'],
            [['dis_order', 'display', 'type', 'is_need'], 'integer'],
            [['reg_field_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reg_field_name' => 'Reg Field Name',
            'dis_order' => 'Dis Order',
            'display' => 'Display',
            'type' => 'Type',
            'is_need' => 'Is Need',
        ];
    }
}
