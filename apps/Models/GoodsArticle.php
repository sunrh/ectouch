<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%goods_article}}".
 *
 * @property integer $goods_id
 * @property integer $article_id
 * @property integer $admin_id
 */
class GoodsArticle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'article_id', 'admin_id'], 'required'],
            [['goods_id', 'article_id', 'admin_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'article_id' => 'Article ID',
            'admin_id' => 'Admin ID',
        ];
    }
}
