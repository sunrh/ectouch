<?php

namespace App\Http\Controllers;

/**
 * 支付配送DEMO
 * Class MyshipController
 * @package App\Http\Controllers
 */
class MyshipController extends Controller
{
    public function actionIndex()
    {
        load_helper(['order', 'transaction']);
        load_lang(['flow', 'user']);

        if (session('user_id') > 0) {
            $consignee_list = get_consignee_list(session('user_id'));

            $choose['country'] = isset($_POST['country']) ? intval($_POST['country']) : $consignee_list[0]['country'];
            $choose['province'] = isset($_POST['province']) ? intval($_POST['province']) : $consignee_list[0]['province'];
            $choose['city'] = isset($_POST['city']) ? intval($_POST['city']) : $consignee_list[0]['city'];
            $choose['district'] = isset($_POST['district']) ? intval($_POST['district']) : (isset($consignee_list[0]['district']) ? $consignee_list[0]['district'] : 0);
        } else {
            $choose['country'] = isset($_POST['country']) ? intval($_POST['country']) : $GLOBALS['_CFG']['shop_country'];
            $choose['province'] = isset($_POST['province']) ? intval($_POST['province']) : 2;
            $choose['city'] = isset($_POST['city']) ? intval($_POST['city']) : 35;
            $choose['district'] = isset($_POST['district']) ? intval($_POST['district']) : 417;
        }

        assign_template();
        assign_dynamic('myship');
        $position = assign_ur_here(0, $GLOBALS['_LANG']['shopping_myship']);
        $this->smarty->assign('page_title', $position['title']);    // 页面标题
        $this->smarty->assign('ur_here', $position['ur_here']);  // 当前位置

        $this->smarty->assign('helps', get_shop_help());       // 网店帮助
        $this->smarty->assign('lang', $GLOBALS['_LANG']);

        $this->smarty->assign('choose', $choose);

        $province_list[null] = get_regions(1, $choose['country']);
        $city_list[null] = get_regions(2, $choose['province']);
        $district_list[null] = get_regions(3, $choose['city']);

        $this->smarty->assign('province_list', $province_list);
        $this->smarty->assign('city_list', $city_list);
        $this->smarty->assign('district_list', $district_list);

        /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
        $this->smarty->assign('country_list', get_regions());

        /* 取得配送列表 */
        $region = array($choose['country'], $choose['province'], $choose['city'], $choose['district']);
        $shipping_list = available_shipping_list($region);
        $cart_weight_price = 0;
        $insure_disabled = true;
        $cod_disabled = true;

        foreach ($shipping_list as $key => $val) {
            $shipping_cfg = unserialize_config($val['configure']);
            $shipping_fee = shipping_fee($val['shipping_code'], unserialize($val['configure']),
                $cart_weight_price['weight'], $cart_weight_price['amount']);

            $shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee, false);
            $shipping_list[$key]['fee'] = $shipping_fee;
            $shipping_list[$key]['free_money'] = price_format($shipping_cfg['free_money'], false);
            $shipping_list[$key]['insure_formated'] = strpos($val['insure'], '%') === false ?
                price_format($val['insure'], false) : $val['insure'];
        }

        $this->smarty->assign('shipping_list', $shipping_list);

        $this->smarty->display('myship.dwt');
    }
}
