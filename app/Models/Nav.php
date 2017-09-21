<?php

namespace App\Models;

use Yii;

/**
 * This is the model class for table "{{%nav}}".
 *
 * @property integer $id
 * @property string $ctype
 * @property integer $cid
 * @property string $name
 * @property integer $ifshow
 * @property integer $vieworder
 * @property integer $opennew
 * @property string $url
 * @property string $type
 */
class Nav extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%nav}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'ifshow', 'vieworder', 'opennew'], 'integer'],
            [['name', 'ifshow', 'vieworder', 'opennew', 'url', 'type'], 'required'],
            [['ctype', 'type'], 'string', 'max' => 10],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ctype' => 'Ctype',
            'cid' => 'Cid',
            'name' => 'Name',
            'ifshow' => 'Ifshow',
            'vieworder' => 'Vieworder',
            'opennew' => 'Opennew',
            'url' => 'Url',
            'type' => 'Type',
        ];
    }
}
