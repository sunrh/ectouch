<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property integer $tag_id
 * @property integer $user_id
 * @property integer $goods_id
 * @property string $tag_words
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'goods_id'], 'integer'],
            [['tag_words'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'user_id' => 'User ID',
            'goods_id' => 'Goods ID',
            'tag_words' => 'Tag Words',
        ];
    }
}
