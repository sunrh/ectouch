<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property integer $comment_id
 * @property integer $comment_type
 * @property integer $id_value
 * @property string $email
 * @property string $user_name
 * @property string $content
 * @property integer $comment_rank
 * @property integer $add_time
 * @property string $ip_address
 * @property integer $status
 * @property integer $parent_id
 * @property integer $user_id
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_type', 'id_value', 'comment_rank', 'add_time', 'status', 'parent_id', 'user_id'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['email', 'user_name'], 'string', 'max' => 60],
            [['ip_address'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'comment_id' => 'Comment ID',
            'comment_type' => 'Comment Type',
            'id_value' => 'Id Value',
            'email' => 'Email',
            'user_name' => 'User Name',
            'content' => 'Content',
            'comment_rank' => 'Comment Rank',
            'add_time' => 'Add Time',
            'ip_address' => 'Ip Address',
            'status' => 'Status',
            'parent_id' => 'Parent ID',
            'user_id' => 'User ID',
        ];
    }
}
