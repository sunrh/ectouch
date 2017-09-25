<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%ad_custom}}".
 *
 * @property integer $ad_id
 * @property integer $ad_type
 * @property string $ad_name
 * @property integer $add_time
 * @property string $content
 * @property string $url
 * @property integer $ad_status
 */
class AdCustom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ad_custom}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_type', 'add_time', 'ad_status'], 'integer'],
            [['content'], 'string'],
            [['ad_name'], 'string', 'max' => 60],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ad_id' => 'Ad ID',
            'ad_type' => 'Ad Type',
            'ad_name' => 'Ad Name',
            'add_time' => 'Add Time',
            'content' => 'Content',
            'url' => 'Url',
            'ad_status' => 'Ad Status',
        ];
    }
}
