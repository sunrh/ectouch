<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class GroupBuyController
 * @package App\Modules\Admin\Controllers
 */
class GroupBuyController extends Controller
{
    public function actionIndex()
    {


        /**
         *  管理中心团购商品管理
         */
        load_helper(['goods', 'order']);

        /* 检查权限 */
        admin_priv('group_by');

        /*------------------------------------------------------ */
//-- 团购活动列表
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'list') {
            /* 模板赋值 */
            $this->smarty->assign('full_page', 1);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['group_buy_list']);
            $this->smarty->assign('action_link', array('href' => 'group_buy.php?act=add', 'text' => $GLOBALS['_LANG']['add_group_buy']));

            $list = $this->group_buy_list();

            $this->smarty->assign('group_buy_list', $list['item']);
            $this->smarty->assign('filter', $list['filter']);
            $this->smarty->assign('record_count', $list['record_count']);
            $this->smarty->assign('page_count', $list['page_count']);

            $sort_flag = sort_flag($list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            /* 显示商品列表页面 */

            $this->smarty->display('group_buy_list.htm');
        }
        if ($_REQUEST['act'] == 'query') {
            $list = $this->group_buy_list();

            $this->smarty->assign('group_buy_list', $list['item']);
            $this->smarty->assign('filter', $list['filter']);
            $this->smarty->assign('record_count', $list['record_count']);
            $this->smarty->assign('page_count', $list['page_count']);

            $sort_flag = sort_flag($list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('group_buy_list.htm'), '',
                array('filter' => $list['filter'], 'page_count' => $list['page_count']));
        }

        /*------------------------------------------------------ */
//-- 添加/编辑团购活动
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
            /* 初始化/取得团购活动信息 */
            if ($_REQUEST['act'] == 'add') {
                $group_buy = array(
                    'act_id' => 0,
                    'start_time' => date('Y-m-d', time() + 86400),
                    'end_time' => date('Y-m-d', time() + 4 * 86400),
                    'price_ladder' => array(array('amount' => 0, 'price' => 0))
                );
            } else {
                $group_buy_id = intval($_REQUEST['id']);
                if ($group_buy_id <= 0) {
                    die('invalid param');
                }
                $group_buy = group_buy_info($group_buy_id);
            }
            $this->smarty->assign('group_buy', $group_buy);

            /* 模板赋值 */
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['add_group_buy']);
            $this->smarty->assign('action_link', $this->list_link($_REQUEST['act'] == 'add'));
            $this->smarty->assign('cat_list', cat_list());
            $this->smarty->assign('brand_list', get_brand_list());

            /* 显示模板 */

            $this->smarty->display('group_buy_info.htm');
        }

        /*------------------------------------------------------ */
//-- 添加/编辑团购活动的提交
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'insert_update') {
            /* 取得团购活动id */
            $group_buy_id = intval($_POST['act_id']);
            if (isset($_POST['finish']) || isset($_POST['succeed']) || isset($_POST['fail']) || isset($_POST['mail'])) {
                if ($group_buy_id <= 0) {
                    sys_msg($GLOBALS['_LANG']['error_group_buy'], 1);
                }
                $group_buy = group_buy_info($group_buy_id);
                if (empty($group_buy)) {
                    sys_msg($GLOBALS['_LANG']['error_group_buy'], 1);
                }
            }

            if (isset($_POST['finish'])) {
                /* 判断订单状态 */
                if ($group_buy['status'] != GBS_UNDER_WAY) {
                    sys_msg($GLOBALS['_LANG']['error_status'], 1);
                }

                /* 结束团购活动，修改结束时间为当前时间 */
                $sql = "UPDATE " . $this->ecs->table('goods_activity') .
                    " SET end_time = '" . gmtime() . "' " .
                    "WHERE act_id = '$group_buy_id' LIMIT 1";
                $this->db->query($sql);

                /* 清除缓存 */
                clear_cache_files();

                /* 提示信息 */
                $links = array(
                    array('href' => 'group_buy.php?act=list', 'text' => $GLOBALS['_LANG']['back_list'])
                );
                sys_msg($GLOBALS['_LANG']['edit_success'], 0, $links);
            } elseif (isset($_POST['succeed'])) {
                /* 设置活动成功 */

                /* 判断订单状态 */
                if ($group_buy['status'] != GBS_FINISHED) {
                    sys_msg($GLOBALS['_LANG']['error_status'], 1);
                }

                /* 如果有订单，更新订单信息 */
                if ($group_buy['total_order'] > 0) {
                    /* 查找该团购活动的已确认或未确认订单（已取消的就不管了） */
                    $sql = "SELECT order_id " .
                        "FROM " . $this->ecs->table('order_info') .
                        " WHERE extension_code = 'group_buy' " .
                        "AND extension_id = '$group_buy_id' " .
                        "AND (order_status = '" . OS_CONFIRMED . "' or order_status = '" . OS_UNCONFIRMED . "')";
                    $order_id_list = $this->db->getCol($sql);

                    /* 更新订单商品价 */
                    $final_price = $group_buy['trans_price'];
                    $sql = "UPDATE " . $this->ecs->table('order_goods') .
                        " SET goods_price = '$final_price' " .
                        "WHERE order_id " . db_create_in($order_id_list);
                    $this->db->query($sql);

                    /* 查询订单商品总额 */
                    $sql = "SELECT order_id, SUM(goods_number * goods_price) AS goods_amount " .
                        "FROM " . $this->ecs->table('order_goods') .
                        " WHERE order_id " . db_create_in($order_id_list) .
                        " GROUP BY order_id";
                    $res = $this->db->query($sql);
                    foreach ($res as $row) {
                        $order_id = $row['order_id'];
                        $goods_amount = floatval($row['goods_amount']);

                        /* 取得订单信息 */
                        $order = order_info($order_id);

                        /* 判断订单是否有效：余额支付金额 + 已付款金额 >= 保证金 */
                        if ($order['surplus'] + $order['money_paid'] >= $group_buy['deposit']) {
                            /* 有效，设为已确认，更新订单 */

                            // 更新商品总额
                            $order['goods_amount'] = $goods_amount;

                            // 如果保价，重新计算保价费用
                            if ($order['insure_fee'] > 0) {
                                $shipping = shipping_info($order['shipping_id']);
                                $order['insure_fee'] = shipping_insure_fee($shipping['shipping_code'], $goods_amount, $shipping['insure']);
                            }

                            // 重算支付费用
                            $order['order_amount'] = $order['goods_amount'] + $order['shipping_fee']
                                + $order['insure_fee'] + $order['pack_fee'] + $order['card_fee']
                                - $order['money_paid'] - $order['surplus'];
                            if ($order['order_amount'] > 0) {
                                $order['pay_fee'] = pay_fee($order['pay_id'], $order['order_amount']);
                            } else {
                                $order['pay_fee'] = 0;
                            }

                            // 计算应付款金额
                            $order['order_amount'] += $order['pay_fee'];

                            // 计算付款状态
                            if ($order['order_amount'] > 0) {
                                $order['pay_status'] = PS_UNPAYED;
                                $order['pay_time'] = 0;
                            } else {
                                $order['pay_status'] = PS_PAYED;
                                $order['pay_time'] = gmtime();
                            }

                            // 如果需要退款，退到帐户余额
                            if ($order['order_amount'] < 0) {
                                // todo （现在手工退款）
                            }

                            // 订单状态
                            $order['order_status'] = OS_CONFIRMED;
                            $order['confirm_time'] = gmtime();

                            // 更新订单
                            $order = addslashes_deep($order);
                            update_order($order_id, $order);
                        } else {
                            /* 无效，取消订单，退回已付款 */

                            // 修改订单状态为已取消，付款状态为未付款
                            $order['order_status'] = OS_CANCELED;
                            $order['to_buyer'] = $GLOBALS['_LANG']['cancel_order_reason'];
                            $order['pay_status'] = PS_UNPAYED;
                            $order['pay_time'] = 0;

                            /* 如果使用余额或有已付款金额，退回帐户余额 */
                            $money = $order['surplus'] + $order['money_paid'];
                            if ($money > 0) {
                                $order['surplus'] = 0;
                                $order['money_paid'] = 0;
                                $order['order_amount'] = $money;

                                // 退款到帐户余额
                                order_refund($order, 1, $GLOBALS['_LANG']['cancel_order_reason'] . ':' . $order['order_sn']);
                            }

                            /* 更新订单 */
                            $order = addslashes_deep($order);
                            update_order($order['order_id'], $order);
                        }
                    }
                }

                /* 修改团购活动状态为成功 */
                $sql = "UPDATE " . $this->ecs->table('goods_activity') .
                    " SET is_finished = '" . GBS_SUCCEED . "' " .
                    "WHERE act_id = '$group_buy_id' LIMIT 1";
                $this->db->query($sql);

                /* 清除缓存 */
                clear_cache_files();

                /* 提示信息 */
                $links = array(
                    array('href' => 'group_buy.php?act=list', 'text' => $GLOBALS['_LANG']['back_list'])
                );
                sys_msg($GLOBALS['_LANG']['edit_success'], 0, $links);
            } elseif (isset($_POST['fail'])) {
                /* 设置活动失败 */

                /* 判断订单状态 */
                if ($group_buy['status'] != GBS_FINISHED) {
                    sys_msg($GLOBALS['_LANG']['error_status'], 1);
                }

                /* 如果有有效订单，取消订单 */
                if ($group_buy['valid_order'] > 0) {
                    /* 查找未确认或已确认的订单 */
                    $sql = "SELECT * " .
                        "FROM " . $this->ecs->table('order_info') .
                        " WHERE extension_code = 'group_buy' " .
                        "AND extension_id = '$group_buy_id' " .
                        "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "') ";
                    $res = $this->db->query($sql);
                    foreach ($res as $order) {
                        // 修改订单状态为已取消，付款状态为未付款
                        $order['order_status'] = OS_CANCELED;
                        $order['to_buyer'] = $GLOBALS['_LANG']['cancel_order_reason'];
                        $order['pay_status'] = PS_UNPAYED;
                        $order['pay_time'] = 0;

                        /* 如果使用余额或有已付款金额，退回帐户余额 */
                        $money = $order['surplus'] + $order['money_paid'];
                        if ($money > 0) {
                            $order['surplus'] = 0;
                            $order['money_paid'] = 0;
                            $order['order_amount'] = $money;

                            // 退款到帐户余额
                            order_refund($order, 1, $GLOBALS['_LANG']['cancel_order_reason'] . ':' . $order['order_sn'], $money);
                        }

                        /* 更新订单 */
                        $order = addslashes_deep($order);
                        update_order($order['order_id'], $order);
                    }
                }

                /* 修改团购活动状态为失败，记录失败原因（活动说明） */
                $sql = "UPDATE " . $this->ecs->table('goods_activity') .
                    " SET is_finished = '" . GBS_FAIL . "', " .
                    "act_desc = '$_POST[act_desc]' " .
                    "WHERE act_id = '$group_buy_id' LIMIT 1";
                $this->db->query($sql);

                /* 清除缓存 */
                clear_cache_files();

                /* 提示信息 */
                $links = array(
                    array('href' => 'group_buy.php?act=list', 'text' => $GLOBALS['_LANG']['back_list'])
                );
                sys_msg($GLOBALS['_LANG']['edit_success'], 0, $links);
            } elseif (isset($_POST['mail'])) {
                /* 发送通知邮件 */

                /* 判断订单状态 */
                if ($group_buy['status'] != GBS_SUCCEED) {
                    sys_msg($GLOBALS['_LANG']['error_status'], 1);
                }

                /* 取得邮件模板 */
                $tpl = get_mail_template('group_buy');

                /* 初始化订单数和成功发送邮件数 */
                $count = 0;
                $send_count = 0;

                /* 取得有效订单 */
                $sql = "SELECT o.consignee, o.add_time, g.goods_number, o.order_sn, " .
                    "o.order_amount, o.order_id, o.email " .
                    "FROM " . $this->ecs->table('order_info') . " AS o, " .
                    $this->ecs->table('order_goods') . " AS g " .
                    "WHERE o.order_id = g.order_id " .
                    "AND o.extension_code = 'group_buy' " .
                    "AND o.extension_id = '$group_buy_id' " .
                    "AND o.order_status = '" . OS_CONFIRMED . "'";
                $res = $this->db->query($sql);
                foreach ($res as $order) {
                    /* 邮件模板赋值 */
                    $this->smarty->assign('consignee', $order['consignee']);
                    $this->smarty->assign('add_time', local_date($GLOBALS['_CFG']['time_format'], $order['add_time']));
                    $this->smarty->assign('goods_name', $group_buy['goods_name']);
                    $this->smarty->assign('goods_number', $order['goods_number']);
                    $this->smarty->assign('order_sn', $order['order_sn']);
                    $this->smarty->assign('order_amount', price_format($order['order_amount']));
                    $this->smarty->assign('shop_url', $this->ecs->url() . 'user.php?act=order_detail&order_id=' . $order['order_id']);
                    $this->smarty->assign('shop_name', $GLOBALS['_CFG']['shop_name']);
                    $this->smarty->assign('send_date', local_date($GLOBALS['_CFG']['date_format']));

                    /* 取得模板内容，发邮件 */
                    $content = $this->smarty->fetch('str:' . $tpl['template_content']);
                    if (send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html'])) {
                        $send_count++;
                    }
                    $count++;
                }

                /* 提示信息 */
                sys_msg(sprintf($GLOBALS['_LANG']['mail_result'], $count, $send_count));
            } else {
                /* 保存团购信息 */
                $goods_id = intval($_POST['goods_id']);
                if ($goods_id <= 0) {
                    sys_msg($GLOBALS['_LANG']['error_goods_null']);
                }
                $info = $this->goods_group_buy($goods_id);
                if ($info && $info['act_id'] != $group_buy_id) {
                    sys_msg($GLOBALS['_LANG']['error_goods_exist']);
                }

                $goods_name = $this->db->getOne("SELECT goods_name FROM " . $this->ecs->table('goods') . " WHERE goods_id = '$goods_id'");

                $act_name = empty($_POST['act_name']) ? $goods_name : sub_str($_POST['act_name'], 0, 255, false);

                $deposit = floatval($_POST['deposit']);
                if ($deposit < 0) {
                    $deposit = 0;
                }

                $restrict_amount = intval($_POST['restrict_amount']);
                if ($restrict_amount < 0) {
                    $restrict_amount = 0;
                }

                $gift_integral = intval($_POST['gift_integral']);
                if ($gift_integral < 0) {
                    $gift_integral = 0;
                }

                $price_ladder = array();
                $count = count($_POST['ladder_amount']);
                for ($i = $count - 1; $i >= 0; $i--) {
                    /* 如果数量小于等于0，不要 */
                    $amount = intval($_POST['ladder_amount'][$i]);
                    if ($amount <= 0) {
                        continue;
                    }

                    /* 如果价格小于等于0，不要 */
                    $price = round(floatval($_POST['ladder_price'][$i]), 2);
                    if ($price <= 0) {
                        continue;
                    }

                    /* 加入价格阶梯 */
                    $price_ladder[$amount] = array('amount' => $amount, 'price' => $price);
                }
                if (count($price_ladder) < 1) {
                    sys_msg($GLOBALS['_LANG']['error_price_ladder']);
                }

                /* 限购数量不能小于价格阶梯中的最大数量 */
                $amount_list = array_keys($price_ladder);
                if ($restrict_amount > 0 && max($amount_list) > $restrict_amount) {
                    sys_msg($GLOBALS['_LANG']['error_restrict_amount']);
                }

                ksort($price_ladder);
                $price_ladder = array_values($price_ladder);

                /* 检查开始时间和结束时间是否合理 */
                $start_time = local_strtotime($_POST['start_time']);
                $end_time = local_strtotime($_POST['end_time']);
                if ($start_time >= $end_time) {
                    sys_msg($GLOBALS['_LANG']['invalid_time']);
                }

                $group_buy = array(
                    'act_name' => $act_name,
                    'act_desc' => $_POST['act_desc'],
                    'act_type' => GAT_GROUP_BUY,
                    'goods_id' => $goods_id,
                    'goods_name' => $goods_name,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'ext_info' => serialize(array(
                        'price_ladder' => $price_ladder,
                        'restrict_amount' => $restrict_amount,
                        'gift_integral' => $gift_integral,
                        'deposit' => $deposit
                    ))
                );

                /* 清除缓存 */
                clear_cache_files();

                /* 保存数据 */
                if ($group_buy_id > 0) {
                    /* update */
                    $this->db->autoExecute($this->ecs->table('goods_activity'), $group_buy, 'UPDATE', "act_id = '$group_buy_id'");

                    /* log */
                    admin_log(addslashes($goods_name) . '[' . $group_buy_id . ']', 'edit', 'group_buy');

                    /* todo 更新活动表 */

                    /* 提示信息 */
                    $links = array(
                        array('href' => 'group_buy.php?act=list&' . list_link_postfix(), 'text' => $GLOBALS['_LANG']['back_list'])
                    );
                    sys_msg($GLOBALS['_LANG']['edit_success'], 0, $links);
                } else {
                    /* insert */
                    $this->db->autoExecute($this->ecs->table('goods_activity'), $group_buy, 'INSERT');

                    /* log */
                    admin_log(addslashes($goods_name), 'add', 'group_buy');

                    /* 提示信息 */
                    $links = array(
                        array('href' => 'group_buy.php?act=add', 'text' => $GLOBALS['_LANG']['continue_add']),
                        array('href' => 'group_buy.php?act=list', 'text' => $GLOBALS['_LANG']['back_list'])
                    );
                    sys_msg($GLOBALS['_LANG']['add_success'], 0, $links);
                }
            }
        }

        /*------------------------------------------------------ */
//-- 批量删除团购活动
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'batch_drop') {
            if (isset($_POST['checkboxes'])) {
                $del_count = 0; //初始化删除数量
                foreach ($_POST['checkboxes'] as $key => $id) {
                    /* 取得团购活动信息 */
                    $group_buy = group_buy_info($id);

                    /* 如果团购活动已经有订单，不能删除 */
                    if ($group_buy['valid_order'] <= 0) {
                        /* 删除团购活动 */
                        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_activity') .
                            " WHERE act_id = '$id' LIMIT 1";
                        $GLOBALS['db']->query($sql, 'SILENT');

                        admin_log(addslashes($group_buy['goods_name']) . '[' . $id . ']', 'remove', 'group_buy');
                        $del_count++;
                    }
                }

                /* 如果删除了团购活动，清除缓存 */
                if ($del_count > 0) {
                    clear_cache_files();
                }

                $links[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'group_buy.php?act=list');
                sys_msg(sprintf($GLOBALS['_LANG']['batch_drop_success'], $del_count), 0, $links);
            } else {
                $links[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'group_buy.php?act=list');
                sys_msg($GLOBALS['_LANG']['no_select_group_buy'], 0, $links);
            }
        }

        /*------------------------------------------------------ */
//-- 搜索商品
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'search_goods') {
            check_authz_json('group_by');

            // include_once(ROOT_PATH . 'includes/cls_json.php');

            $json = new Json();
            $filter = $json->decode($_GET['JSON']);
            $arr = get_goods_list($filter);

            make_json_result($arr);
        }

        /*------------------------------------------------------ */
//-- 编辑保证金
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_deposit') {
            check_authz_json('group_by');

            $id = intval($_POST['id']);
            $val = floatval($_POST['val']);

            $sql = "SELECT ext_info FROM " . $this->ecs->table('goods_activity') .
                " WHERE act_id = '$id' AND act_type = '" . GAT_GROUP_BUY . "'";
            $ext_info = unserialize($this->db->getOne($sql));
            $ext_info['deposit'] = $val;

            $sql = "UPDATE " . $this->ecs->table('goods_activity') .
                " SET ext_info = '" . serialize($ext_info) . "'" .
                " WHERE act_id = '$id'";
            $this->db->query($sql);

            clear_cache_files();

            make_json_result(number_format($val, 2));
        }

        /*------------------------------------------------------ */
//-- 编辑保证金
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_restrict_amount') {
            check_authz_json('group_by');

            $id = intval($_POST['id']);
            $val = intval($_POST['val']);

            $sql = "SELECT ext_info FROM " . $this->ecs->table('goods_activity') .
                " WHERE act_id = '$id' AND act_type = '" . GAT_GROUP_BUY . "'";
            $ext_info = unserialize($this->db->getOne($sql));
            $ext_info['restrict_amount'] = $val;

            $sql = "UPDATE " . $this->ecs->table('goods_activity') .
                " SET ext_info = '" . serialize($ext_info) . "'" .
                " WHERE act_id = '$id'";
            $this->db->query($sql);

            clear_cache_files();

            make_json_result($val);
        }

        /*------------------------------------------------------ */
//-- 删除团购活动
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('group_by');

            $id = intval($_GET['id']);

            /* 取得团购活动信息 */
            $group_buy = group_buy_info($id);

            /* 如果团购活动已经有订单，不能删除 */
            if ($group_buy['valid_order'] > 0) {
                make_json_error($GLOBALS['_LANG']['error_exist_order']);
            }

            /* 删除团购活动 */
            $sql = "DELETE FROM " . $this->ecs->table('goods_activity') . " WHERE act_id = '$id' LIMIT 1";
            $this->db->query($sql);

            admin_log(addslashes($group_buy['goods_name']) . '[' . $id . ']', 'remove', 'group_buy');

            clear_cache_files();

            $url = 'group_buy.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

            ecs_header("Location: $url\n");
            exit;
        }
    }

    /*
     * 取得团购活动列表
     * @return   array
     */
    private function group_buy_list()
    {
        $result = get_filter();
        if ($result === false) {
            /* 过滤条件 */
            $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
            if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
                $filter['keyword'] = json_str_iconv($filter['keyword']);
            }
            $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'act_id' : trim($_REQUEST['sort_by']);
            $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

            $where = (!empty($filter['keyword'])) ? " AND goods_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'" : '';

            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods_activity') .
                " WHERE act_type = '" . GAT_GROUP_BUY . "' $where";
            $filter['record_count'] = $GLOBALS['db']->getOne($sql);

            /* 分页大小 */
            $filter = page_and_size($filter);

            /* 查询 */
            $sql = "SELECT * " .
                "FROM " . $GLOBALS['ecs']->table('goods_activity') .
                " WHERE act_type = '" . GAT_GROUP_BUY . "' $where " .
                " ORDER BY $filter[sort_by] $filter[sort_order] " .
                " LIMIT " . $filter['start'] . ", $filter[page_size]";

            $filter['keyword'] = stripslashes($filter['keyword']);
            set_filter($filter, $sql);
        } else {
            $sql = $result['sql'];
            $filter = $result['filter'];
        }
        $res = $GLOBALS['db']->query($sql);

        $list = array();
        foreach ($res as $row) {
            $ext_info = unserialize($row['ext_info']);
            $stat = group_buy_stat($row['act_id'], $ext_info['deposit']);
            $arr = array_merge($row, $stat, $ext_info);

            /* 处理价格阶梯 */
            $price_ladder = $arr['price_ladder'];
            if (!is_array($price_ladder) || empty($price_ladder)) {
                $price_ladder = array(array('amount' => 0, 'price' => 0));
            } else {
                foreach ($price_ladder as $key => $amount_price) {
                    $price_ladder[$key]['formated_price'] = price_format($amount_price['price']);
                }
            }

            /* 计算当前价 */
            $cur_price = $price_ladder[0]['price'];    // 初始化
            $cur_amount = $stat['valid_goods'];         // 当前数量
            foreach ($price_ladder as $amount_price) {
                if ($cur_amount >= $amount_price['amount']) {
                    $cur_price = $amount_price['price'];
                } else {
                    break;
                }
            }

            $arr['cur_price'] = $cur_price;

            $status = group_buy_status($arr);

            $arr['start_time'] = local_date($GLOBALS['_CFG']['date_format'], $arr['start_time']);
            $arr['end_time'] = local_date($GLOBALS['_CFG']['date_format'], $arr['end_time']);
            $arr['cur_status'] = $GLOBALS['_LANG']['gbs'][$status];

            $list[] = $arr;
        }
        $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }

    /**
     * 取得某商品的团购活动
     * @param   int $goods_id 商品id
     * @return  array
     */
    private function goods_group_buy($goods_id)
    {
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods_activity') .
            " WHERE goods_id = '$goods_id' " .
            " AND act_type = '" . GAT_GROUP_BUY . "'" .
            " AND start_time <= " . gmtime() .
            " AND end_time >= " . gmtime();

        return $GLOBALS['db']->getRow($sql);
    }

    /**
     * 列表链接
     * @param   bool $is_add 是否添加（插入）
     * @return  array('href' => $href, 'text' => $text)
     */
    private function list_link($is_add = true)
    {
        $href = 'group_buy.php?act=list';
        if (!$is_add) {
            $href .= '&' . list_link_postfix();
        }

        return array('href' => $href, 'text' => $GLOBALS['_LANG']['group_buy_list']);
    }
}