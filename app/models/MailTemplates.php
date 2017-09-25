<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%mail_templates}}".
 *
 * @property integer $template_id
 * @property string $template_code
 * @property integer $is_html
 * @property string $template_subject
 * @property string $template_content
 * @property integer $last_modify
 * @property integer $last_send
 * @property string $type
 */
class MailTemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mail_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_html', 'last_modify', 'last_send'], 'integer'],
            [['template_content', 'type'], 'required'],
            [['template_content'], 'string'],
            [['template_code'], 'string', 'max' => 30],
            [['template_subject'], 'string', 'max' => 200],
            [['type'], 'string', 'max' => 10],
            [['template_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template_id' => 'Template ID',
            'template_code' => 'Template Code',
            'is_html' => 'Is Html',
            'template_subject' => 'Template Subject',
            'template_content' => 'Template Content',
            'last_modify' => 'Last Modify',
            'last_send' => 'Last Send',
            'type' => 'Type',
        ];
    }
}
