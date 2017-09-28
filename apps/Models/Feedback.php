<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%feedback}}".
 *
 * @property integer $msg_id
 * @property integer $parent_id
 * @property integer $user_id
 * @property string $user_name
 * @property string $user_email
 * @property string $msg_title
 * @property integer $msg_type
 * @property integer $msg_status
 * @property string $msg_content
 * @property integer $msg_time
 * @property string $message_img
 * @property integer $order_id
 * @property integer $msg_area
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feedback}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'user_id', 'msg_type', 'msg_status', 'msg_time', 'order_id', 'msg_area'], 'integer'],
            [['msg_content'], 'required'],
            [['msg_content'], 'string'],
            [['user_name', 'user_email'], 'string', 'max' => 60],
            [['msg_title'], 'string', 'max' => 200],
            [['message_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msg_id' => 'Msg ID',
            'parent_id' => 'Parent ID',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_email' => 'User Email',
            'msg_title' => 'Msg Title',
            'msg_type' => 'Msg Type',
            'msg_status' => 'Msg Status',
            'msg_content' => 'Msg Content',
            'msg_time' => 'Msg Time',
            'message_img' => 'Message Img',
            'order_id' => 'Order ID',
            'msg_area' => 'Msg Area',
        ];
    }
}
