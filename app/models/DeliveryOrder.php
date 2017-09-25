<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%delivery_order}}".
 *
 * @property integer $delivery_id
 * @property string $delivery_sn
 * @property string $order_sn
 * @property integer $order_id
 * @property string $invoice_no
 * @property integer $add_time
 * @property integer $shipping_id
 * @property string $shipping_name
 * @property integer $user_id
 * @property string $action_user
 * @property string $consignee
 * @property string $address
 * @property integer $country
 * @property integer $province
 * @property integer $city
 * @property integer $district
 * @property string $sign_building
 * @property string $email
 * @property string $zipcode
 * @property string $tel
 * @property string $mobile
 * @property string $best_time
 * @property string $postscript
 * @property string $how_oos
 * @property string $insure_fee
 * @property string $shipping_fee
 * @property integer $update_time
 * @property integer $suppliers_id
 * @property integer $status
 * @property integer $agency_id
 */
class DeliveryOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%delivery_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['delivery_sn', 'order_sn'], 'required'],
            [['order_id', 'add_time', 'shipping_id', 'user_id', 'country', 'province', 'city', 'district', 'update_time', 'suppliers_id', 'status', 'agency_id'], 'integer'],
            [['insure_fee', 'shipping_fee'], 'number'],
            [['delivery_sn', 'order_sn'], 'string', 'max' => 20],
            [['invoice_no'], 'string', 'max' => 50],
            [['shipping_name', 'sign_building', 'best_time', 'how_oos'], 'string', 'max' => 120],
            [['action_user'], 'string', 'max' => 30],
            [['consignee', 'email', 'zipcode', 'tel', 'mobile'], 'string', 'max' => 60],
            [['address'], 'string', 'max' => 250],
            [['postscript'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'delivery_id' => 'Delivery ID',
            'delivery_sn' => 'Delivery Sn',
            'order_sn' => 'Order Sn',
            'order_id' => 'Order ID',
            'invoice_no' => 'Invoice No',
            'add_time' => 'Add Time',
            'shipping_id' => 'Shipping ID',
            'shipping_name' => 'Shipping Name',
            'user_id' => 'User ID',
            'action_user' => 'Action User',
            'consignee' => 'Consignee',
            'address' => 'Address',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'sign_building' => 'Sign Building',
            'email' => 'Email',
            'zipcode' => 'Zipcode',
            'tel' => 'Tel',
            'mobile' => 'Mobile',
            'best_time' => 'Best Time',
            'postscript' => 'Postscript',
            'how_oos' => 'How Oos',
            'insure_fee' => 'Insure Fee',
            'shipping_fee' => 'Shipping Fee',
            'update_time' => 'Update Time',
            'suppliers_id' => 'Suppliers ID',
            'status' => 'Status',
            'agency_id' => 'Agency ID',
        ];
    }
}
