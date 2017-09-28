<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%cat_recommend}}".
 *
 * @property integer $cat_id
 * @property integer $recommend_type
 */
class CatRecommend extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cat_recommend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'recommend_type'], 'required'],
            [['cat_id', 'recommend_type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cat_id' => 'Cat ID',
            'recommend_type' => 'Recommend Type',
        ];
    }
}
