<?php

namespace app\api\models\v2;

use Yii;

/**
 * This is the model class for table "ecs_ad_position".
 *
 * @property integer $position_id
 * @property string $position_name
 * @property integer $ad_width
 * @property integer $ad_height
 * @property string $position_desc
 * @property string $position_style
 */
class AdPosition extends Foundation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ad_position}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ad_width', 'ad_height'], 'integer'],
            [['position_style'], 'required'],
            [['position_style'], 'string'],
            [['position_name'], 'string', 'max' => 60],
            [['position_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'position_id' => 'Position ID',
            'position_name' => 'Position Name',
            'ad_width' => 'Ad Width',
            'ad_height' => 'Ad Height',
            'position_desc' => 'Position Desc',
            'position_style' => 'Position Style',
        ];
    }

    public function getPosition()
    {
        return $this->hasOne('app\api\models\v2\Ad', 'position_id', 'position_id');
    }
}
