<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%attribute}}".
 *
 * @property integer $attr_id
 * @property integer $cat_id
 * @property string $attr_name
 * @property integer $attr_input_type
 * @property integer $attr_type
 * @property string $attr_values
 * @property integer $attr_index
 * @property integer $sort_order
 * @property integer $is_linked
 * @property integer $attr_group
 */
class Attribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attribute}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'attr_input_type', 'attr_type', 'attr_index', 'sort_order', 'is_linked', 'attr_group'], 'integer'],
            [['attr_values'], 'required'],
            [['attr_values'], 'string'],
            [['attr_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'attr_id' => 'Attr ID',
            'cat_id' => 'Cat ID',
            'attr_name' => 'Attr Name',
            'attr_input_type' => 'Attr Input Type',
            'attr_type' => 'Attr Type',
            'attr_values' => 'Attr Values',
            'attr_index' => 'Attr Index',
            'sort_order' => 'Sort Order',
            'is_linked' => 'Is Linked',
            'attr_group' => 'Attr Group',
        ];
    }
}
