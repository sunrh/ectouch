<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%crons}}".
 *
 * @property integer $cron_id
 * @property string $cron_code
 * @property string $cron_name
 * @property string $cron_desc
 * @property integer $cron_order
 * @property string $cron_config
 * @property integer $thistime
 * @property integer $nextime
 * @property integer $day
 * @property string $week
 * @property string $hour
 * @property string $minute
 * @property integer $enable
 * @property integer $run_once
 * @property string $allow_ip
 * @property string $alow_files
 */
class Crons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%crons}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cron_code', 'cron_name', 'cron_config', 'nextime', 'day', 'week', 'hour', 'minute', 'alow_files'], 'required'],
            [['cron_desc', 'cron_config'], 'string'],
            [['cron_order', 'thistime', 'nextime', 'day', 'enable', 'run_once'], 'integer'],
            [['cron_code'], 'string', 'max' => 20],
            [['cron_name'], 'string', 'max' => 120],
            [['week'], 'string', 'max' => 1],
            [['hour'], 'string', 'max' => 2],
            [['minute', 'alow_files'], 'string', 'max' => 255],
            [['allow_ip'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cron_id' => 'Cron ID',
            'cron_code' => 'Cron Code',
            'cron_name' => 'Cron Name',
            'cron_desc' => 'Cron Desc',
            'cron_order' => 'Cron Order',
            'cron_config' => 'Cron Config',
            'thistime' => 'Thistime',
            'nextime' => 'Nextime',
            'day' => 'Day',
            'week' => 'Week',
            'hour' => 'Hour',
            'minute' => 'Minute',
            'enable' => 'Enable',
            'run_once' => 'Run Once',
            'allow_ip' => 'Allow Ip',
            'alow_files' => 'Alow Files',
        ];
    }
}
