<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%stats}}".
 *
 * @property integer $access_time
 * @property string $ip_address
 * @property integer $visit_times
 * @property string $browser
 * @property string $system
 * @property string $language
 * @property string $area
 * @property string $referer_domain
 * @property string $referer_path
 * @property string $access_url
 */
class Stats extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%stats}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_time', 'visit_times'], 'integer'],
            [['ip_address'], 'string', 'max' => 15],
            [['browser'], 'string', 'max' => 60],
            [['system', 'language'], 'string', 'max' => 20],
            [['area'], 'string', 'max' => 30],
            [['referer_domain'], 'string', 'max' => 100],
            [['referer_path'], 'string', 'max' => 200],
            [['access_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access_time' => 'Access Time',
            'ip_address' => 'Ip Address',
            'visit_times' => 'Visit Times',
            'browser' => 'Browser',
            'system' => 'System',
            'language' => 'Language',
            'area' => 'Area',
            'referer_domain' => 'Referer Domain',
            'referer_path' => 'Referer Path',
            'access_url' => 'Access Url',
        ];
    }
}
