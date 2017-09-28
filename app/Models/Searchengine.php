<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%searchengine}}".
 *
 * @property string $date
 * @property string $searchengine
 * @property integer $count
 */
class Searchengine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%searchengine}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'searchengine'], 'required'],
            [['date'], 'safe'],
            [['count'], 'integer'],
            [['searchengine'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'searchengine' => 'Searchengine',
            'count' => 'Count',
        ];
    }
}
