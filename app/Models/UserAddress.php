<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_address}}".
 *
 * @property integer $address_id
 * @property string $address_name
 * @property integer $user_id
 * @property string $consignee
 * @property string $email
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $address
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $sign_building
 * @property string $best_time
 */
class UserAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'country', 'province', 'city', 'district'], 'integer'],
            [['address_name'], 'string', 'max' => 50],
            [['consignee', 'email', 'zipcode', 'tel', 'mobile'], 'string', 'max' => 60],
            [['address', 'sign_building', 'best_time'], 'string', 'max' => 120],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'address_id' => 'Address ID',
            'address_name' => 'Address Name',
            'user_id' => 'User ID',
            'consignee' => 'Consignee',
            'email' => 'Email',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'address' => 'Address',
            'zipcode' => 'Zipcode',
            'tel' => 'Tel',
            'mobile' => 'Mobile',
            'sign_building' => 'Sign Building',
            'best_time' => 'Best Time',
        ];
    }
}
