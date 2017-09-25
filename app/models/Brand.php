<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%brand}}".
 *
 * @property integer $brand_id
 * @property string $brand_name
 * @property string $brand_logo
 * @property string $brand_desc
 * @property string $site_url
 * @property integer $sort_order
 * @property integer $is_show
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_desc'], 'required'],
            [['brand_desc'], 'string'],
            [['sort_order', 'is_show'], 'integer'],
            [['brand_name'], 'string', 'max' => 60],
            [['brand_logo'], 'string', 'max' => 80],
            [['site_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'brand_logo' => 'Brand Logo',
            'brand_desc' => 'Brand Desc',
            'site_url' => 'Site Url',
            'sort_order' => 'Sort Order',
            'is_show' => 'Is Show',
        ];
    }
}
