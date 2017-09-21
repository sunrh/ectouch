<?php

namespace App\Http\Controllers;

/**
 * 处理收回确认的页面
 * Class ReceiveController
 * @package App\Http\Controllers
 */
class ReceiveController extends Controller
{
    public function actionIndex()
    {
        /* 取得参数 */
        $order_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  // 订单号
        $consignee = !empty($_REQUEST['con']) ? rawurldecode(trim($_REQUEST['con'])) : ''; // 收货人

        /* 查询订单信息 */
        $sql = 'SELECT * FROM ' . $this->ecs->table('order_info') . " WHERE order_id = '$order_id'";
        $order = $this->db->getRow($sql);

        if (empty($order)) {
            $msg = $GLOBALS['_LANG']['order_not_exists'];
        } /* 检查订单 */
        elseif ($order['shipping_status'] == SS_RECEIVED) {
            $msg = $GLOBALS['_LANG']['order_already_received'];
        } elseif ($order['shipping_status'] != SS_SHIPPED) {
            $msg = $GLOBALS['_LANG']['order_invalid'];
        } elseif ($order['consignee'] != $consignee) {
            $msg = $GLOBALS['_LANG']['order_invalid'];
        } else {
            /* 修改订单发货状态为“确认收货” */
            $sql = "UPDATE " . $this->ecs->table('order_info') . " SET shipping_status = '" . SS_RECEIVED . "' WHERE order_id = '$order_id'";
            $this->db->query($sql);

            /* 记录日志 */
            order_action($order['order_sn'], $order['order_status'], SS_RECEIVED, $order['pay_status'], '', $GLOBALS['_LANG']['buyer']);

            $msg = $GLOBALS['_LANG']['act_ok'];
        }

        /* 显示模板 */
        assign_template();
        $position = assign_ur_here();
        $this->smarty->assign('page_title', $position['title']);    // 页面标题
        $this->smarty->assign('ur_here', $position['ur_here']);  // 当前位置

        $this->smarty->assign('categories', get_categories_tree()); // 分类树
        $this->smarty->assign('helps', get_shop_help());       // 网店帮助

        assign_dynamic('receive');

        $this->smarty->assign('msg', $msg);
        $this->smarty->display('receive.dwt');
    }
}
