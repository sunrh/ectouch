<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%admin_message}}".
 *
 * @property integer $message_id
 * @property integer $sender_id
 * @property integer $receiver_id
 * @property integer $sent_time
 * @property integer $read_time
 * @property integer $readed
 * @property integer $deleted
 * @property string $title
 * @property string $message
 */
class AdminMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'receiver_id', 'sent_time', 'read_time', 'readed', 'deleted'], 'integer'],
            [['message'], 'required'],
            [['message'], 'string'],
            [['title'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'sender_id' => 'Sender ID',
            'receiver_id' => 'Receiver ID',
            'sent_time' => 'Sent Time',
            'read_time' => 'Read Time',
            'readed' => 'Readed',
            'deleted' => 'Deleted',
            'title' => 'Title',
            'message' => 'Message',
        ];
    }
}
