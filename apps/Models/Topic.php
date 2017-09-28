<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%topic}}".
 *
 * @property integer $topic_id
 * @property string $title
 * @property string $intro
 * @property integer $start_time
 * @property integer $end_time
 * @property string $data
 * @property string $template
 * @property string $css
 * @property string $topic_img
 * @property string $title_pic
 * @property string $base_style
 * @property string $htmls
 * @property string $keywords
 * @property string $description
 */
class Topic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro', 'data', 'css'], 'required'],
            [['intro', 'data', 'css', 'htmls'], 'string'],
            [['start_time', 'end_time'], 'integer'],
            [['title', 'template', 'topic_img', 'title_pic', 'keywords', 'description'], 'string', 'max' => 255],
            [['base_style'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'topic_id' => 'Topic ID',
            'title' => 'Title',
            'intro' => 'Intro',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'data' => 'Data',
            'template' => 'Template',
            'css' => 'Css',
            'topic_img' => 'Topic Img',
            'title_pic' => 'Title Pic',
            'base_style' => 'Base Style',
            'htmls' => 'Htmls',
            'keywords' => 'Keywords',
            'description' => 'Description',
        ];
    }
}
