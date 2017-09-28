<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Exchange;

/**
 * Class PaymentController
 * @package App\Modules\Admin\Controllers
 */
class PaymentController extends Controller
{
    public function actionIndex()
    {
        /**
         *  支付方式管理程序
         */
        $exc = new Exchange($this->ecs->table('payment'), $this->db, 'pay_code', 'pay_name');

        /**
         * 支付方式列表 ?act=list
         */
        if ($_REQUEST['act'] == 'list') {
            /* 查询数据库中启用的支付方式 */
            $pay_list = array();
            $sql = "SELECT * FROM " . $this->ecs->table('payment') . " WHERE enabled = '1' ORDER BY pay_order";
            $res = $this->db->query($sql);
            foreach ($res as $row) {
                $pay_list[$row['pay_code']] = $row;
            }

            /* 取得插件文件中的支付方式 */
            $modules = read_modules(app_path('Plugins/Payment'));
            for ($i = 0; $i < count($modules); $i++) {
                $code = strtolower($modules[$i]['code']);
                $modules[$i]['pay_code'] = $modules[$i]['code'];
                /* 如果数据库中有，取数据库中的名称和描述 */
                if (isset($pay_list[$code])) {
                    $modules[$i]['name'] = $pay_list[$code]['pay_name'];
                    $modules[$i]['pay_fee'] = $pay_list[$code]['pay_fee'];
                    $modules[$i]['is_cod'] = $pay_list[$code]['is_cod'];
                    $modules[$i]['desc'] = $pay_list[$code]['pay_desc'];
                    $modules[$i]['pay_order'] = $pay_list[$code]['pay_order'];
                    $modules[$i]['install'] = '1';
                } else {
                    $modules[$i]['name'] = $GLOBALS['_LANG'][$code];
                    if (!isset($modules[$i]['pay_fee'])) {
                        $modules[$i]['pay_fee'] = 0;
                    }
                    $modules[$i]['desc'] = $GLOBALS['_LANG'][$modules[$i]['desc']];
                    $modules[$i]['install'] = '0';
                }
            }

            load_helper('compositor');

            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['02_payment_list']);
            $this->smarty->assign('modules', $modules);
            $this->smarty->display('payment_list.htm');
        }

        /*------------------------------------------------------ */
//-- 安装支付方式 ?act=install&code=".$code."
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'install') {
            admin_priv('payment');

            /* 取相应插件信息 */
            $set_modules = true;
            include_once(ROOT_PATH . 'includes/modules/payment/' . $_REQUEST['code'] . '.php');

            $data = $modules[0];
            /* 对支付费用判断。如果data['pay_fee']为false无支付费用，为空则说明以配送有关，其它可以修改 */
            if (isset($data['pay_fee'])) {
                $data['pay_fee'] = trim($data['pay_fee']);
            } else {
                $data['pay_fee'] = 0;
            }

            $pay['pay_code'] = $data['code'];
            $pay['pay_name'] = $GLOBALS['_LANG'][$data['code']];
            $pay['pay_desc'] = $GLOBALS['_LANG'][$data['desc']];
            $pay['is_cod'] = $data['is_cod'];
            $pay['pay_fee'] = $data['pay_fee'];
            $pay['is_online'] = $data['is_online'];
            $pay['pay_config'] = array();

            foreach ($data['config'] as $key => $value) {
                $config_desc = (isset($GLOBALS['_LANG'][$value['name'] . '_desc'])) ? $GLOBALS['_LANG'][$value['name'] . '_desc'] : '';
                $pay['pay_config'][$key] = $value +
                    array('label' => $GLOBALS['_LANG'][$value['name']], 'value' => $value['value'], 'desc' => $config_desc);

                if ($pay['pay_config'][$key]['type'] == 'select' ||
                    $pay['pay_config'][$key]['type'] == 'radiobox') {
                    $pay['pay_config'][$key]['range'] = $GLOBALS['_LANG'][$pay['pay_config'][$key]['name'] . '_range'];
                }
            }


            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['02_payment_list'], 'href' => 'payment.php?act=list'));
            $this->smarty->assign('pay', $pay);
            $this->smarty->display('payment_edit.htm');
        }
        if ($_REQUEST['act'] == 'get_config') {
            check_authz_json('payment');

            $code = $_REQUEST['code'];

            /* 取相应插件信息 */
            $set_modules = true;
            include_once(ROOT_PATH . 'includes/modules/payment/' . $code . '.php');
            $data = $modules[0]['config'];
            $config = '<table>';
            $range = '';
            foreach ($data as $key => $value) {
                $config .= "<tr><td width=80><span class='label'>";
                $config .= $GLOBALS['_LANG'][$data[$key]['name']];
                $config .= "</span></td>";
                if ($data[$key]['type'] == 'text') {
                    if ($data[$key]['name'] == 'alipay_account') {
                        $config .= "<td><input name='cfg_value[]' type='text' value='" . $data[$key]['value'] . "' /><a href=\"https://www.alipay.com/himalayas/practicality.htm\" target=\"_blank\">" . $GLOBALS['_LANG']['alipay_look'] . "</a></td>";
                    } elseif ($data[$key]['name'] == 'tenpay_account') {
                        $config .= "<td><input name='cfg_value[]' type='text' value='" . $data[$key]['value'] . "' />" . $GLOBALS['_LANG']['penpay_register'] . "</td>";
                    } else {
                        $config .= "<td><input name='cfg_value[]' type='text' value='" . $data[$key]['value'] . "' /></td>";
                    }
                } elseif ($data[$key]['type'] == 'select') {
                    $range = $GLOBALS['_LANG'][$data[$key]['name'] . '_range'];
                    $config .= "<td><select name='cfg_value[]'>";
                    foreach ($range as $index => $val) {
                        $config .= "<option value='$index'>" . $range[$index] . "</option>";
                    }
                    $config .= "</select></td>";
                }
                $config .= "</tr>";
                //$config .= '<br />';
                $config .= "<input name='cfg_name[]' type='hidden' value='" . $data[$key]['name'] . "' />";
                $config .= "<input name='cfg_type[]' type='hidden' value='" . $data[$key]['type'] . "' />";
                $config .= "<input name='cfg_lang[]' type='hidden' value='" . $data[$key]['lang'] . "' />";
            }
            $config .= '</table>';

            make_json_result($config);
        }

        /*------------------------------------------------------ */
//-- 编辑支付方式 ?act=edit&code={$code}
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit') {
            admin_priv('payment');

            /* 查询该支付方式内容 */
            if (isset($_REQUEST['code'])) {
                $_REQUEST['code'] = trim($_REQUEST['code']);
            } else {
                die('invalid parameter');
            }

            $sql = "SELECT * FROM " . $this->ecs->table('payment') . " WHERE pay_code = '$_REQUEST[code]' AND enabled = '1'";
            $pay = $this->db->getRow($sql);
            if (empty($pay)) {
                $links[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'payment.php?act=list');
                sys_msg($GLOBALS['_LANG']['payment_not_available'], 0, $links);
            }

            /* 取相应插件信息 */
            $set_modules = true;
            include_once(ROOT_PATH . 'includes/modules/payment/' . $_REQUEST['code'] . '.php');
            $data = $modules[0];

            /* 取得配置信息 */
            if (is_string($pay['pay_config'])) {
                $store = unserialize($pay['pay_config']);
                /* 取出已经设置属性的code */
                $code_list = array();
                foreach ($store as $key => $value) {
                    $code_list[$value['name']] = $value['value'];
                }
                $pay['pay_config'] = array();

                /* 循环插件中所有属性 */
                foreach ($data['config'] as $key => $value) {
                    $pay['pay_config'][$key]['desc'] = (isset($GLOBALS['_LANG'][$value['name'] . '_desc'])) ? $GLOBALS['_LANG'][$value['name'] . '_desc'] : '';
                    $pay['pay_config'][$key]['label'] = $GLOBALS['_LANG'][$value['name']];
                    $pay['pay_config'][$key]['name'] = $value['name'];
                    $pay['pay_config'][$key]['type'] = $value['type'];

                    if (isset($code_list[$value['name']])) {
                        $pay['pay_config'][$key]['value'] = $code_list[$value['name']];
                    } else {
                        $pay['pay_config'][$key]['value'] = $value['value'];
                    }

                    if ($pay['pay_config'][$key]['type'] == 'select' ||
                        $pay['pay_config'][$key]['type'] == 'radiobox') {
                        $pay['pay_config'][$key]['range'] = $GLOBALS['_LANG'][$pay['pay_config'][$key]['name'] . '_range'];
                    }
                }
            }

            /* 如果以前没设置支付费用，编辑时补上 */
            if (!isset($pay['pay_fee'])) {
                if (isset($data['pay_fee'])) {
                    $pay['pay_fee'] = $data['pay_fee'];
                } else {
                    $pay['pay_fee'] = 0;
                }
            }


            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['02_payment_list'], 'href' => 'payment.php?act=list'));
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['edit'] . $GLOBALS['_LANG']['payment']);
            $this->smarty->assign('pay', $pay);
            $this->smarty->display('payment_edit.htm');
        }

        /*------------------------------------------------------ */
//-- 提交支付方式 post
        /*------------------------------------------------------ */
        elseif (isset($_POST['Submit'])) {
            admin_priv('payment');

            /* 检查输入 */
            if (empty($_POST['pay_name'])) {
                sys_msg($GLOBALS['_LANG']['payment_name'] . $GLOBALS['_LANG']['empty']);
            }

            $sql = "SELECT COUNT(*) FROM " . $this->ecs->table('payment') .
                " WHERE pay_name = '$_POST[pay_name]' AND pay_code <> '$_POST[pay_code]'";
            if ($this->db->GetOne($sql) > 0) {
                sys_msg($GLOBALS['_LANG']['payment_name'] . $GLOBALS['_LANG']['repeat'], 1);
            }

            /* 取得配置信息 */
            $pay_config = array();
            if (isset($_POST['cfg_value']) && is_array($_POST['cfg_value'])) {
                for ($i = 0; $i < count($_POST['cfg_value']); $i++) {
                    $pay_config[] = array('name' => trim($_POST['cfg_name'][$i]),
                        'type' => trim($_POST['cfg_type'][$i]),
                        'value' => trim($_POST['cfg_value'][$i])
                    );
                }
            }
            $pay_config = serialize($pay_config);
            /* 取得和验证支付手续费 */
            $pay_fee = empty($_POST['pay_fee']) ? 0 : $_POST['pay_fee'];

            /* 检查是编辑还是安装 */
            $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'payment.php?act=list');
            if ($_POST['pay_id']) {
                /* 编辑 */
                $sql = "UPDATE " . $this->ecs->table('payment') .
                    "SET pay_name = '$_POST[pay_name]'," .
                    "    pay_desc = '$_POST[pay_desc]'," .
                    "    pay_config = '$pay_config', " .
                    "    pay_fee    =  '$pay_fee' " .
                    "WHERE pay_code = '$_POST[pay_code]' LIMIT 1";
                $this->db->query($sql);

                /* 记录日志 */
                admin_log($_POST['pay_name'], 'edit', 'payment');

                sys_msg($GLOBALS['_LANG']['edit_ok'], 0, $link);
            } else {
                /* 安装，检查该支付方式是否曾经安装过 */
                $sql = "SELECT COUNT(*) FROM " . $this->ecs->table('payment') . " WHERE pay_code = '$_REQUEST[pay_code]'";
                if ($this->db->GetOne($sql) > 0) {
                    /* 该支付方式已经安装过, 将该支付方式的状态设置为 enable */
                    $sql = "UPDATE " . $this->ecs->table('payment') .
                        "SET pay_name = '$_POST[pay_name]'," .
                        "    pay_desc = '$_POST[pay_desc]'," .
                        "    pay_config = '$pay_config'," .
                        "    pay_fee    =  '$pay_fee', " .
                        "    enabled = '1' " .
                        "WHERE pay_code = '$_POST[pay_code]' LIMIT 1";
                    $this->db->query($sql);
                } else {
                    /* 该支付方式没有安装过, 将该支付方式的信息添加到数据库 */
                    $sql = "INSERT INTO " . $this->ecs->table('payment') . " (pay_code, pay_name, pay_desc, pay_config, is_cod, pay_fee, enabled, is_online)" .
                        "VALUES ('$_POST[pay_code]', '$_POST[pay_name]', '$_POST[pay_desc]', '$pay_config', '$_POST[is_cod]', '$pay_fee', 1, '$_POST[is_online]')";
                    $this->db->query($sql);
                }

                /* 记录日志 */
                admin_log($_POST['pay_name'], 'install', 'payment');

                sys_msg($GLOBALS['_LANG']['install_ok'], 0, $link);
            }
        }

        /*------------------------------------------------------ */
//-- 卸载支付方式 ?act=uninstall&code={$code}
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'uninstall') {
            admin_priv('payment');

            /* 把 enabled 设为 0 */
            $sql = "UPDATE " . $this->ecs->table('payment') .
                "SET enabled = '0' " .
                "WHERE pay_code = '$_REQUEST[code]' LIMIT 1";
            $this->db->query($sql);

            /* 记录日志 */
            admin_log($_REQUEST['code'], 'uninstall', 'payment');

            $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'payment.php?act=list');
            sys_msg($GLOBALS['_LANG']['uninstall_ok'], 0, $link);
        }

        /*------------------------------------------------------ */
//-- 修改支付方式名称
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_name') {
            /* 检查权限 */
            check_authz_json('payment');

            /* 取得参数 */
            $code = json_str_iconv(trim($_POST['id']));
            $name = json_str_iconv(trim($_POST['val']));

            /* 检查名称是否为空 */
            if (empty($name)) {
                make_json_error($GLOBALS['_LANG']['name_is_null']);
            }

            /* 检查名称是否重复 */
            if (!$exc->is_only('pay_name', $name, $code)) {
                make_json_error($GLOBALS['_LANG']['name_exists']);
            }

            /* 更新支付方式名称 */
            $exc->edit("pay_name = '$name'", $code);
            make_json_result(stripcslashes($name));
        }

        /*------------------------------------------------------ */
//-- 修改支付方式描述
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_desc') {
            /* 检查权限 */
            check_authz_json('payment');

            /* 取得参数 */
            $code = json_str_iconv(trim($_POST['id']));
            $desc = json_str_iconv(trim($_POST['val']));

            /* 更新描述 */
            $exc->edit("pay_desc = '$desc'", $code);
            make_json_result(stripcslashes($desc));
        }

        /*------------------------------------------------------ */
//-- 修改支付方式排序
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_order') {
            /* 检查权限 */
            check_authz_json('payment');

            /* 取得参数 */
            $code = json_str_iconv(trim($_POST['id']));
            $order = intval($_POST['val']);

            /* 更新排序 */
            $exc->edit("pay_order = '$order'", $code);
            make_json_result(stripcslashes($order));
        }

        /*------------------------------------------------------ */
//-- 修改支付方式费用
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_pay_fee') {
            /* 检查权限 */
            check_authz_json('payment');

            /* 取得参数 */
            $code = json_str_iconv(trim($_POST['id']));
            $pay_fee = json_str_iconv(trim($_POST['val']));
            if (empty($pay_fee)) {
                $pay_fee = 0;
            } else {
                $pay_fee = make_semiangle($pay_fee); //全角转半角
                if (strpos($pay_fee, '%') === false) {
                    $pay_fee = floatval($pay_fee);
                } else {
                    $pay_fee = floatval($pay_fee) . '%';
                }
            }

            /* 更新支付费用 */
            $exc->edit("pay_fee = '$pay_fee'", $code);
            make_json_result(stripcslashes($pay_fee));
        }
    }
}