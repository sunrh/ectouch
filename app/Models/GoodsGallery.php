<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%goods_gallery}}".
 *
 * @property integer $img_id
 * @property integer $goods_id
 * @property string $img_url
 * @property string $img_desc
 * @property string $thumb_url
 * @property string $img_original
 */
class GoodsGallery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%goods_gallery}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['img_url', 'img_desc', 'thumb_url', 'img_original'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'img_id' => 'Img ID',
            'goods_id' => 'Goods ID',
            'img_url' => 'Img Url',
            'img_desc' => 'Img Desc',
            'thumb_url' => 'Thumb Url',
            'img_original' => 'Img Original',
        ];
    }
}
