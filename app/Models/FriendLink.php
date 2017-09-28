<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%friend_link}}".
 *
 * @property integer $link_id
 * @property string $link_name
 * @property string $link_url
 * @property string $link_logo
 * @property integer $show_order
 */
class FriendLink extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%friend_link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['show_order'], 'integer'],
            [['link_name', 'link_url', 'link_logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'link_id' => 'Link ID',
            'link_name' => 'Link Name',
            'link_url' => 'Link Url',
            'link_logo' => 'Link Logo',
            'show_order' => 'Show Order',
        ];
    }
}
