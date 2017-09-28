<?php

namespace App\Api\Models;

use Yii;
use App\Api\Libraries\Token;

/**
 * This is the model class for table "{{%shipping}}".
 *
 * @property integer $shipping_id
 * @property string $shipping_code
 * @property string $shipping_name
 * @property string $shipping_desc
 * @property string $insure
 * @property integer $support_cod
 * @property integer $enabled
 * @property string $shipping_print
 * @property string $print_bg
 * @property string $config_lable
 * @property integer $print_model
 * @property integer $shipping_order
 */
class Shipping extends Foundation
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shipping}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['support_cod', 'enabled', 'print_model', 'shipping_order'], 'integer'],
            [['shipping_print'], 'required'],
            [['shipping_print', 'config_lable'], 'string'],
            [['shipping_code'], 'string', 'max' => 20],
            [['shipping_name'], 'string', 'max' => 120],
            [['shipping_desc', 'print_bg'], 'string', 'max' => 255],
            [['insure'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shipping_id' => 'Shipping ID',
            'shipping_code' => 'Shipping Code',
            'shipping_name' => 'Shipping Name',
            'shipping_desc' => 'Shipping Desc',
            'insure' => 'Insure',
            'support_cod' => 'Support Cod',
            'enabled' => 'Enabled',
            'shipping_print' => 'Shipping Print',
            'print_bg' => 'Print Bg',
            'config_lable' => 'Config Lable',
            'print_model' => 'Print Model',
            'shipping_order' => 'Shipping Order',
        ];
    }

    public static function total_shipping_fee($address, $products, $shipping_id)
    {
        $uid = Token::authorization();
        $prefix = Yii::$app->db->tablePrefix;
        $weight = 0;
        $amount = 0;
        $number = 0;

        //如果传products对象 json后数组

        $IsShippingFree = true;

        if (isset($products)) {
            //            $products = json_decode($products, true);

            foreach ($products as $product) {
                $goods_weight = Goods::find()
                    ->select(['goods_weight'])
                    ->where(['goods_id' => $product['goods_id']])
                    ->column();

                $goods_weight = (count($goods_weight) > 0) ? $goods_weight[0] : 0;

                if ($goods_weight) {
                    $weight += $goods_weight * $product['goods_number'];
                }
                $amount += Goods::get_final_price($product['goods_id'], $product['goods_number']);
                $number += $product['goods_number'];

                if (!intval($product['is_shipping'])) {
                    $IsShippingFree = false;
                }
            }
        }

        // 查看购物车中是否全为免运费商品，若是则把运费赋为零
        if ($IsShippingFree) {
            return 0;
        }

        //
        if (!empty($address)) {
            $region_id_list = UserAddress::getRegionIdList($address);
        }

        $model = Shipping::find()
            ->select([$prefix.'shipping_area.configure', $prefix.'shipping.shipping_code'])
            ->leftJoin($prefix.'shipping_area', $prefix.'shipping_area.shipping_id = ' . $prefix . 'shipping.shipping_id')
            ->leftJoin($prefix.'area_region', $prefix.'area_region.shipping_area_id = ' . $prefix . 'shipping_area.shipping_area_id')
            ->andWhere([$prefix.'shipping.enabled' => 1])
            ->andWhere([$prefix.'shipping.shipping_id' => $shipping_id]);

        if (!empty($region_id_list)) {
            $model->andWhere(['in', $prefix.'area_region.region_id', $region_id_list]);
        }

        $result = $model->asArray()->one();
        if (!empty($result['configure'])) {
            $configure = self::getConfigure($result['configure']);
            $fee = self::calculate($configure, $result['shipping_code'], $weight, $amount, $number);
            return Goods::price_format($fee, false);
        }
        return false;
    }
    /**
     * 查询所有配送方式
     */
    public static function getAllShipping()
    {
        $shipping = self::find()
            ->where(['enabled' => 1])
            ->asArray()
            ->all();

        if (count($shipping) > 0) {
            return $shipping;
        }
        return self::formatBody(self::BAD_REQUEST, trans('message.shipping.error'));
    }

    /**
     * 计算订单的配送费用的函数
     *
     */
    private static function calculate($configure, $shipping_code, $goods_weight, $goods_amount, $goods_number)
    {
        $fee = 0;
        if ($configure['free_money'] > 0 && $goods_amount >= $configure['free_money']) {
            return $fee;
        }

        switch ($shipping_code) {
            case 'city_express':
            case 'flat':
                $fee = isset($configure['base_fee']) ? $configure['base_fee'] : 0;
                break;

            case 'ems':
                $fee = isset($configure['base_fee']) ? $configure['base_fee'] : 0;
                $configure['fee_compute_mode'] = !empty($configure['fee_compute_mode']) ? $configure['fee_compute_mode'] : 'by_weight';

                if ($configure['fee_compute_mode'] == 'by_number') {
                    $fee = $goods_number * $configure['item_fee'];
                } else {
                    if ($goods_weight > 0.5) {
                        $fee += (ceil(($goods_weight - 0.5) / 0.5)) * $configure['step_fee'];
                    }
                }
                break;

            case 'post_express':
                $fee = isset($configure['base_fee']) ? $configure['base_fee'] : 0;
                $configure['fee_compute_mode'] = !empty($configure['fee_compute_mode']) ? $configure['fee_compute_mode'] : 'by_weight';

                if ($configure['fee_compute_mode'] == 'by_number') {
                    $fee = $goods_number * $configure['item_fee'];
                } else {
                    if ($goods_weight > 5) {
                        $fee += 8 * $configure['step_fee'];
                        $fee += (ceil(($goods_weight - 5) / 0.5)) * $configure['step_fee1'];
                    } else {
                        if ($goods_weight > 1) {
                            $fee += (ceil(($goods_weight - 1) / 0.5)) * $configure['step_fee'];
                        }
                    }
                }
                break;

            case 'post_mail':
                $fee = $configure['base_fee'] + $configure['pack_fee'];
                $configure['fee_compute_mode'] = !empty($configure['fee_compute_mode']) ? $configure['fee_compute_mode'] : 'by_weight';

                if ($configure['fee_compute_mode'] == 'by_number') {
                    $fee = $goods_number * ($configure['item_fee'] + $configure['pack_fee']);
                } else {
                    if ($goods_weight > 5) {
                        $fee += 4 * $configure['step_fee'];
                        $fee += (ceil(($goods_weight - 5))) * $configure['step_fee1'];
                    } else {
                        if ($goods_weight > 1) {
                            $fee += (ceil(($goods_weight - 1))) * $configure['step_fee'];
                        }
                    }
                }
                break;
            case 'presswork':
                $fee = $goods_weight * 4 + 3.4;

                if ($goods_weight > 0.1) {
                    $fee += (ceil(($goods_weight - 0.1) / 0.1)) * 0.4;
                }
                break;

            case 'sf_express':
            case 'sto_express':
            case 'yto':
                if ($configure['free_money'] > 0 && $goods_amount >= $configure['free_money']) {
                    return 0;
                } else {
                    $fee = isset($configure['base_fee']) ? $configure['base_fee'] : 0;
                    $configure['fee_compute_mode'] = !empty($configure['fee_compute_mode']) ? $configure['fee_compute_mode'] : 'by_weight';

                    if ($configure['fee_compute_mode'] == 'by_number') {
                        $fee = $goods_number * $configure['item_fee'];
                    } else {
                        if ($goods_weight > 1) {
                            $fee += (ceil(($goods_weight - 1))) * $configure['step_fee'];
                        }
                    }
                }
                break;
            case 'zto':
                $fee = isset($configure['base_fee']) ? $configure['base_fee'] : 0;
                $configure['fee_compute_mode'] = !empty($configure['fee_compute_mode']) ? $configure['fee_compute_mode'] : 'by_weight';

                if ($configure['fee_compute_mode'] == 'by_number') {
                    $fee = $goods_number * $configure['item_fee'];
                } else {
                    if ($goods_weight > 1) {
                        $fee += (ceil(($goods_weight - 1))) * $configure['step_fee'];
                    }
                }
                break;

            default:
                $fee = 0;
                break;
        }
        $fee = floatval($fee);

        return $fee;
    }

    /**
     * 取得某配送方式对应于某收货地址的区域配置信息
     *
     */
    private static function getConfigure($configure)
    {
        $data = [];
        $configure = unserialize($configure);
        foreach ($configure as $key => $val) {
            $data[$val['name']] = $val['value'];
        }

        return $data;
    }
}
