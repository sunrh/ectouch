<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%card}}".
 *
 * @property integer $card_id
 * @property string $card_name
 * @property string $card_img
 * @property string $card_fee
 * @property string $free_money
 * @property string $card_desc
 */
class Card extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_fee', 'free_money'], 'number'],
            [['card_name'], 'string', 'max' => 120],
            [['card_img', 'card_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'card_id' => 'Card ID',
            'card_name' => 'Card Name',
            'card_img' => 'Card Img',
            'card_fee' => 'Card Fee',
            'free_money' => 'Free Money',
            'card_desc' => 'Card Desc',
        ];
    }
}
