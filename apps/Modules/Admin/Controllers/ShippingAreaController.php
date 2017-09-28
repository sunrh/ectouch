<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Exchange;

/**
 * Class ShippingAreaController
 * @package App\Modules\Admin\Controllers
 */
class ShippingAreaController extends Controller
{
    public function actionIndex()
    {

        /**
         *  配送区域管理程序
         */


        $exc = new Exchange($this->ecs->table('shipping_area'), $this->db, 'shipping_area_id', 'shipping_area_name');

        /*------------------------------------------------------ */
//-- 配送区域列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            $shipping_id = intval($_REQUEST['shipping']);

            $list = $this->get_shipping_area_list($shipping_id);
            $this->smarty->assign('areas', $list);

            $this->smarty->assign('ur_here', '<a href="shipping.php?act=list">' .
                $GLOBALS['_LANG']['03_shipping_list'] . '</a> - ' . $GLOBALS['_LANG']['shipping_area_list'] . '</a>');
            $this->smarty->assign('action_link', array('href' => 'shipping_area.php?act=add&shipping=' . $shipping_id,
                'text' => $GLOBALS['_LANG']['new_area']));
            $this->smarty->assign('full_page', 1);


            $this->smarty->display('shipping_area_list.htm');
        }

        /*------------------------------------------------------ */
//-- 新建配送区域
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'add' && !empty($_REQUEST['shipping'])) {
            admin_priv('shiparea_manage');

            $shipping = $this->db->getRow("SELECT shipping_name, shipping_code FROM " . $this->ecs->table('shipping') . " WHERE shipping_id='$_REQUEST[shipping]'");

            $set_modules = 1;
            include_once(ROOT_PATH . 'includes/modules/shipping/' . $shipping['shipping_code'] . '.php');

            $fields = array();
            foreach ($modules[0]['configure'] as $key => $val) {
                $fields[$key]['name'] = $val['name'];
                $fields[$key]['value'] = $val['value'];
                $fields[$key]['label'] = $GLOBALS['_LANG'][$val['name']];
            }
            $count = count($fields);
            $fields[$count]['name'] = "free_money";
            $fields[$count]['value'] = "0";
            $fields[$count]['label'] = $GLOBALS['_LANG']["free_money"];

            /* 如果支持货到付款，则允许设置货到付款支付费用 */
            if ($modules[0]['cod']) {
                $count++;
                $fields[$count]['name'] = "pay_fee";
                $fields[$count]['value'] = "0";
                $fields[$count]['label'] = $GLOBALS['_LANG']['pay_fee'];
            }

            $shipping_area['shipping_id'] = 0;
            $shipping_area['free_money'] = 0;

            $this->smarty->assign('ur_here', $shipping['shipping_name'] . ' - ' . $GLOBALS['_LANG']['new_area']);
            $this->smarty->assign('shipping_area', array('shipping_id' => $_REQUEST['shipping'], 'shipping_code' => $shipping['shipping_code']));
            $this->smarty->assign('fields', $fields);
            $this->smarty->assign('form_action', 'insert');
            $this->smarty->assign('countries', get_regions());
            $this->smarty->assign('default_country', $GLOBALS['_CFG']['shop_country']);

            $this->smarty->display('shipping_area_info.htm');
        }
        if ($_REQUEST['act'] == 'insert') {
            admin_priv('shiparea_manage');

            /* 检查同类型的配送方式下有没有重名的配送区域 */
            $sql = "SELECT COUNT(*) FROM " . $this->ecs->table("shipping_area") .
                " WHERE shipping_id='$_POST[shipping]' AND shipping_area_name='$_POST[shipping_area_name]'";
            if ($this->db->getOne($sql) > 0) {
                sys_msg($GLOBALS['_LANG']['repeat_area_name'], 1);
            } else {
                $shipping_code = $this->db->getOne("SELECT shipping_code FROM " . $this->ecs->table('shipping') .
                    " WHERE shipping_id='$_POST[shipping]'");
                $plugin = '../includes/modules/shipping/' . $shipping_code . ".php";

                if (!file_exists($plugin)) {
                    sys_msg($GLOBALS['_LANG']['not_find_plugin'], 1);
                } else {
                    $set_modules = 1;
                    include_once($plugin);
                }

                $config = array();
                foreach ($modules[0]['configure'] as $key => $val) {
                    $config[$key]['name'] = $val['name'];
                    $config[$key]['value'] = $_POST[$val['name']];
                }

                $count = count($config);
                $config[$count]['name'] = 'free_money';
                $config[$count]['value'] = empty($_POST['free_money']) ? '' : $_POST['free_money'];
                $count++;
                $config[$count]['name'] = 'fee_compute_mode';
                $config[$count]['value'] = empty($_POST['fee_compute_mode']) ? '' : $_POST['fee_compute_mode'];
                /* 如果支持货到付款，则允许设置货到付款支付费用 */
                if ($modules[0]['cod']) {
                    $count++;
                    $config[$count]['name'] = 'pay_fee';
                    $config[$count]['value'] = make_semiangle(empty($_POST['pay_fee']) ? '' : $_POST['pay_fee']);
                }

                $sql = "INSERT INTO " . $this->ecs->table('shipping_area') .
                    " (shipping_area_name, shipping_id, configure) " .
                    "VALUES" .
                    " ('$_POST[shipping_area_name]', '$_POST[shipping]', '" . serialize($config) . "')";

                $this->db->query($sql);

                $new_id = $this->db->insert_Id();

                /* 添加选定的城市和地区 */
                if (isset($_POST['regions']) && is_array($_POST['regions'])) {
                    foreach ($_POST['regions'] as $key => $val) {
                        $sql = "INSERT INTO " . $this->ecs->table('area_region') . " (shipping_area_id, region_id) VALUES ('$new_id', '$val')";
                        $this->db->query($sql);
                    }
                }

                admin_log($_POST['shipping_area_name'], 'add', 'shipping_area');

                //$lnk[] = array('text' => $GLOBALS['_LANG']['add_area_region'], 'href'=>'shipping_area.php?act=region&id='.$new_id);
                $lnk[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'shipping_area.php?act=list&shipping=' . $_POST['shipping']);
                $lnk[] = array('text' => $GLOBALS['_LANG']['add_continue'], 'href' => 'shipping_area.php?act=add&shipping=' . $_POST['shipping']);
                sys_msg($GLOBALS['_LANG']['add_area_success'], 0, $lnk);
            }
        }

        /*------------------------------------------------------ */
//-- 编辑配送区域
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit') {
            admin_priv('shiparea_manage');

            $sql = "SELECT a.shipping_name, a.shipping_code, a.support_cod, b.* " .
                "FROM " . $this->ecs->table('shipping') . " AS a, " . $this->ecs->table('shipping_area') . " AS b " .
                "WHERE b.shipping_id=a.shipping_id AND b.shipping_area_id='$_REQUEST[id]'";
            $row = $this->db->getRow($sql);

            $set_modules = 1;
            include_once(ROOT_PATH . 'includes/modules/shipping/' . $row['shipping_code'] . '.php');

            $fields = unserialize($row['configure']);
            /* 如果配送方式支持货到付款并且没有设置货到付款支付费用，则加入货到付款费用 */
            if ($row['support_cod'] && $fields[count($fields) - 1]['name'] != 'pay_fee') {
                $fields[] = array('name' => 'pay_fee', 'value' => 0);
            }

            foreach ($fields as $key => $val) {
                /* 替换更改的语言项 */
                if ($val['name'] == 'basic_fee') {
                    $val['name'] = 'base_fee';
                }
//       if ($val['name'] == 'step_fee1')
//       {
//            $val['name'] = 'step_fee';
//       }
//       if ($val['name'] == 'step_fee2')
//       {
//            $val['name'] = 'step_fee1';
//       }

                if ($val['name'] == 'item_fee') {
                    $item_fee = 1;
                }
                if ($val['name'] == 'fee_compute_mode') {
                    $this->smarty->assign('fee_compute_mode', $val['value']);
                    unset($fields[$key]);
                } else {
                    $fields[$key]['name'] = $val['name'];
                    $fields[$key]['label'] = $GLOBALS['_LANG'][$val['name']];
                }
            }

            if (empty($item_fee)) {
                $field = array('name' => 'item_fee', 'value' => '0', 'label' => empty($GLOBALS['_LANG']['item_fee']) ? '' : $GLOBALS['_LANG']['item_fee']);
                array_unshift($fields, $field);
            }

            /* 获得该区域下的所有地区 */
            $regions = array();

            $sql = "SELECT a.region_id, r.region_name " .
                "FROM " . $this->ecs->table('area_region') . " AS a, " . $this->ecs->table('region') . " AS r " .
                "WHERE r.region_id=a.region_id AND a.shipping_area_id='$_REQUEST[id]'";
            $res = $this->db->query($sql);
            foreach ($res as $arr) {
                $regions[$arr['region_id']] = $arr['region_name'];
            }


            $this->smarty->assign('ur_here', $row['shipping_name'] . ' - ' . $GLOBALS['_LANG']['edit_area']);
            $this->smarty->assign('id', $_REQUEST['id']);
            $this->smarty->assign('fields', $fields);
            $this->smarty->assign('shipping_area', $row);
            $this->smarty->assign('regions', $regions);
            $this->smarty->assign('form_action', 'update');
            $this->smarty->assign('countries', get_regions());
            $this->smarty->assign('default_country', 1);
            $this->smarty->display('shipping_area_info.htm');
        }
        if ($_REQUEST['act'] == 'update') {
            admin_priv('shiparea_manage');

            /* 检查同类型的配送方式下有没有重名的配送区域 */
            $sql = "SELECT COUNT(*) FROM " . $this->ecs->table("shipping_area") .
                " WHERE shipping_id='$_POST[shipping]' AND " .
                "shipping_area_name='$_POST[shipping_area_name]' AND " .
                "shipping_area_id<>'$_POST[id]'";
            if ($this->db->getOne($sql) > 0) {
                sys_msg($GLOBALS['_LANG']['repeat_area_name'], 1);
            } else {
                $shipping_code = $this->db->getOne("SELECT shipping_code FROM " . $this->ecs->table('shipping') . " WHERE shipping_id='$_POST[shipping]'");
                $plugin = '../includes/modules/shipping/' . $shipping_code . ".php";

                if (!file_exists($plugin)) {
                    sys_msg($GLOBALS['_LANG']['not_find_plugin'], 1);
                } else {
                    $set_modules = 1;
                    include_once($plugin);
                }

                $config = array();
                foreach ($modules[0]['configure'] as $key => $val) {
                    $config[$key]['name'] = $val['name'];
                    $config[$key]['value'] = $_POST[$val['name']];
                }

                $count = count($config);
                $config[$count]['name'] = 'free_money';
                $config[$count]['value'] = empty($_POST['free_money']) ? '' : $_POST['free_money'];
                $count++;
                $config[$count]['name'] = 'fee_compute_mode';
                $config[$count]['value'] = empty($_POST['fee_compute_mode']) ? '' : $_POST['fee_compute_mode'];
                if ($modules[0]['cod']) {
                    $count++;
                    $config[$count]['name'] = 'pay_fee';
                    $config[$count]['value'] = make_semiangle(empty($_POST['pay_fee']) ? '' : $_POST['pay_fee']);
                }

                $sql = "UPDATE " . $this->ecs->table('shipping_area') .
                    " SET shipping_area_name='$_POST[shipping_area_name]', " .
                    "configure='" . serialize($config) . "' " .
                    "WHERE shipping_area_id='$_POST[id]'";

                $this->db->query($sql);

                admin_log($_POST['shipping_area_name'], 'edit', 'shipping_area');

                /* 过滤掉重复的region */
                $selected_regions = array();
                if (isset($_POST['regions'])) {
                    foreach ($_POST['regions'] as $region_id) {
                        $selected_regions[$region_id] = $region_id;
                    }
                }

                // 查询所有区域 region_id => parent_id
                $sql = "SELECT region_id, parent_id FROM " . $this->ecs->table('region');
                $res = $this->db->query($sql);
                foreach ($res as $row) {
                    $region_list[$row['region_id']] = $row['parent_id'];
                }

                // 过滤掉上级存在的区域
                foreach ($selected_regions as $region_id) {
                    $id = $region_id;
                    while ($region_list[$id] != 0) {
                        $id = $region_list[$id];
                        if (isset($selected_regions[$id])) {
                            unset($selected_regions[$region_id]);
                            break;
                        }
                    }
                }

                /* 清除原有的城市和地区 */
                $this->db->query("DELETE FROM " . $this->ecs->table("area_region") . " WHERE shipping_area_id='$_POST[id]'");

                /* 添加选定的城市和地区 */
                foreach ($selected_regions as $key => $val) {
                    $sql = "INSERT INTO " . $this->ecs->table('area_region') . " (shipping_area_id, region_id) VALUES ('$_POST[id]', '$val')";
                    $this->db->query($sql);
                }

                $lnk[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'shipping_area.php?act=list&shipping=' . $_POST['shipping']);

                sys_msg($GLOBALS['_LANG']['edit_area_success'], 0, $lnk);
            }
        }

        /*------------------------------------------------------ */
//-- 批量删除配送区域
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'multi_remove') {
            admin_priv('shiparea_manage');

            if (isset($_POST['areas']) && count($_POST['areas']) > 0) {
                $i = 0;
                foreach ($_POST['areas'] as $v) {
                    $this->db->query("DELETE FROM " . $this->ecs->table('shipping_area') . " WHERE shipping_area_id='$v'");
                    $i++;
                }

                /* 记录管理员操作 */
                admin_log('', 'batch_remove', 'shipping_area');
            }
            /* 返回 */
            $links[0] = array('href' => 'shipping_area.php?act=list&shipping=' . intval($_REQUEST['shipping']), 'text' => $GLOBALS['_LANG']['go_back']);
            sys_msg($GLOBALS['_LANG']['remove_success'], 0, $links);
        }

        /*------------------------------------------------------ */
//-- 编辑配送区域名称
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_area') {
            /* 检查权限 */
            check_authz_json('shiparea_manage');

            /* 取得参数 */
            $id = intval($_POST['id']);
            $val = json_str_iconv(trim($_POST['val']));

            /* 取得该区域所属的配送id */
            $shipping_id = $exc->get_name($id, 'shipping_id');

            /* 检查是否有重复的配送区域名称 */
            if (!$exc->is_only('shipping_area_name', $val, $id, "shipping_id = '$shipping_id'")) {
                make_json_error($GLOBALS['_LANG']['repeat_area_name']);
            }

            /* 更新名称 */
            $exc->edit("shipping_area_name = '$val'", $id);

            /* 记录日志 */
            admin_log($val, 'edit', 'shipping_area');

            /* 返回 */
            make_json_result(stripcslashes($val));
        }

        /*------------------------------------------------------ */
//-- 删除配送区域
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'remove_area') {
            check_authz_json('shiparea_manage');

            $id = intval($_GET['id']);
            $name = $exc->get_name($id);
            $shipping_id = $exc->get_name($id, 'shipping_id');

            $exc->drop($id);
            $this->db->query('DELETE FROM ' . $this->ecs->table('area_region') . ' WHERE shipping_area_id=' . $id);

            admin_log($name, 'remove', 'shipping_area');

            $list = $this->get_shipping_area_list($shipping_id);
            $this->smarty->assign('areas', $list);
            make_json_result($this->smarty->fetch('shipping_area_list.htm'));
        }
    }

    /**
     * 取得配送区域列表
     * @param   int $shipping_id 配送id
     */
    private function get_shipping_area_list($shipping_id)
    {
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('shipping_area');
        if ($shipping_id > 0) {
            $sql .= " WHERE shipping_id = '$shipping_id'";
        }
        $res = $GLOBALS['db']->query($sql);
        $list = array();
        foreach ($res as $row) {
            $sql = "SELECT r.region_name " .
                "FROM " . $GLOBALS['ecs']->table('area_region') . " AS a, " .
                $GLOBALS['ecs']->table('region') . " AS r " .
                "WHERE a.region_id = r.region_id " .
                "AND a.shipping_area_id = '$row[shipping_area_id]'";
            $regions = join(', ', $GLOBALS['db']->getCol($sql));

            $row['shipping_area_regions'] = empty($regions) ?
                '<a href="shipping_area.php?act=region&amp;id=' . $row['shipping_area_id'] .
                '" style="color:red">' . $GLOBALS['_LANG']['empty_regions'] . '</a>' : $regions;
            $list[] = $row;
        }

        return $list;
    }
}