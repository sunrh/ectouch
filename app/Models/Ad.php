<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%ad}}".
 *
 * @property integer $ad_id
 * @property integer $position_id
 * @property integer $media_type
 * @property string $ad_name
 * @property string $ad_link
 * @property string $ad_code
 * @property integer $start_time
 * @property integer $end_time
 * @property string $link_man
 * @property string $link_email
 * @property string $link_phone
 * @property integer $click_count
 * @property integer $enabled
 */
class Ad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ad}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_id', 'media_type', 'start_time', 'end_time', 'click_count', 'enabled'], 'integer'],
            [['ad_code'], 'required'],
            [['ad_code'], 'string'],
            [['ad_name', 'link_man', 'link_email', 'link_phone'], 'string', 'max' => 60],
            [['ad_link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ad_id' => 'Ad ID',
            'position_id' => 'Position ID',
            'media_type' => 'Media Type',
            'ad_name' => 'Ad Name',
            'ad_link' => 'Ad Link',
            'ad_code' => 'Ad Code',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'link_man' => 'Link Man',
            'link_email' => 'Link Email',
            'link_phone' => 'Link Phone',
            'click_count' => 'Click Count',
            'enabled' => 'Enabled',
        ];
    }
}
