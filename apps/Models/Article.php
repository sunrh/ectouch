<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $article_id
 * @property integer $cat_id
 * @property string $title
 * @property string $content
 * @property string $author
 * @property string $author_email
 * @property string $keywords
 * @property integer $article_type
 * @property integer $is_open
 * @property integer $add_time
 * @property string $file_url
 * @property integer $open_type
 * @property string $link
 * @property string $description
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'article_type', 'is_open', 'add_time', 'open_type'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 150],
            [['author'], 'string', 'max' => 30],
            [['author_email'], 'string', 'max' => 60],
            [['keywords', 'file_url', 'link', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'Article ID',
            'cat_id' => 'Cat ID',
            'title' => 'Title',
            'content' => 'Content',
            'author' => 'Author',
            'author_email' => 'Author Email',
            'keywords' => 'Keywords',
            'article_type' => 'Article Type',
            'is_open' => 'Is Open',
            'add_time' => 'Add Time',
            'file_url' => 'File Url',
            'open_type' => 'Open Type',
            'link' => 'Link',
            'description' => 'Description',
        ];
    }
}
