<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%adsense}}".
 *
 * @property integer $from_ad
 * @property string $referer
 * @property integer $clicks
 */
class Adsense extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%adsense}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_ad', 'clicks'], 'integer'],
            [['referer'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'from_ad' => 'From Ad',
            'referer' => 'Referer',
            'clicks' => 'Clicks',
        ];
    }
}
