<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class UsersOrderController
 * @package App\Modules\Admin\Controllers
 */
class UsersOrderController extends Controller
{
    public function actionIndex()
    {

        /**
         *  会员排行统计程序
         */

        load_helper('order');
        load_lang('statistic', 'admin');

        $this->smarty->assign('lang', $GLOBALS['_LANG']);

        if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' || $_REQUEST['act'] == 'download')) {
            /* 检查权限 */
            check_authz_json('client_flow_stats');
            if (strstr($_REQUEST['start_date'], '-') === false) {
                $_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
                $_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
            }

            if ($_REQUEST['act'] == 'download') {
                $user_orderinfo = $this->get_user_orderinfo(false);
                $filename = $_REQUEST['start_date'] . '_' . $_REQUEST['end_date'] . 'users_order';

                header("Content-type: application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=$filename.xls");

                $data = "{$GLOBALS['_LANG'][visit_buy]}\t\n";
                $data .= "{$GLOBALS['_LANG'][order_by]}\t{$GLOBALS['_LANG'][member_name]}\t{$GLOBALS['_LANG'][order_amount]}\t{$GLOBALS['_LANG'][buy_sum]}\t\n";

                foreach ($user_orderinfo['user_orderinfo'] AS $k => $row) {
                    $order_by = $k + 1;
                    $data .= "$order_by\t$row[user_name]\t$row[order_num]\t$row[turnover]\n";
                }
                echo ecs_iconv(CHARSET, 'GB2312', $data);
                exit;
            }
            $user_orderinfo = $this->get_user_orderinfo();
            $this->smarty->assign('filter', $user_orderinfo['filter']);
            $this->smarty->assign('record_count', $user_orderinfo['record_count']);
            $this->smarty->assign('page_count', $user_orderinfo['page_count']);
            $this->smarty->assign('user_orderinfo', $user_orderinfo['user_orderinfo']);

            $sort_flag = sort_flag($user_orderinfo['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('users_order.htm'), '', array('filter' => $user_orderinfo['filter'], 'page_count' => $user_orderinfo['page_count']));

        } else {
            /* 权限判断 */
            admin_priv('client_flow_stats');
            /* 时间参数 */
            if (!isset($_REQUEST['start_date'])) {
                $start_date = local_strtotime('-7 days');
            }
            if (!isset($_REQUEST['end_date'])) {
                $end_date = local_strtotime('today');
            }

            /* 取得会员排行数据 */
            $user_orderinfo = $this->get_user_orderinfo();

            /* 赋值到模板 */
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['report_users']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['download_amount_sort'],
                'href' => "#download"));
            $this->smarty->assign('filter', $user_orderinfo['filter']);
            $this->smarty->assign('record_count', $user_orderinfo['record_count']);
            $this->smarty->assign('page_count', $user_orderinfo['page_count']);
            $this->smarty->assign('user_orderinfo', $user_orderinfo['user_orderinfo']);
            $this->smarty->assign('full_page', 1);
            $this->smarty->assign('start_date', local_date('Y-m-d', $start_date));
            $this->smarty->assign('end_date', local_date('Y-m-d', $end_date));
            $this->smarty->assign('sort_order_num', '<img src="'. __TPL__ .'/images/sort_desc.gif">');
            /* 页面显示 */

            $this->smarty->display('users_order.htm');
        }

    }
    /*------------------------------------------------------ */
//--会员排行需要的函数
    /*------------------------------------------------------ */
    /*
     * 取得会员订单量/购物额排名统计数据
     * @param   bool  $is_pagination  是否分页
     * @return  array   取得会员订单量/购物额排名统计数据
     */
    private function get_user_orderinfo($is_pagination = true)
    {
        $filter['start_date'] = empty($_REQUEST['start_date']) ? $start_date : local_strtotime($_REQUEST['start_date']);
        $filter['end_date'] = empty($_REQUEST['end_date']) ? $end_date : local_strtotime($_REQUEST['end_date']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'order_num' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = "WHERE u.user_id = o.user_id " .
            "AND u.user_id > 0 " . order_query_sql('finished', 'o.');
        if ($filter['start_date']) {
            $where .= " AND o.add_time >= '" . $filter['start_date'] . "'";
        }
        if ($filter['end_date']) {
            $where .= " AND o.add_time <= '" . $filter['end_date'] . "'";
        }

        $sql = "SELECT count(distinct(u.user_id)) FROM " . $this->ecs->table('users') . " AS u, " . $this->ecs->table('order_info') . " AS o " . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 计算订单各种费用之和的语句 */
        $total_fee = " SUM(" . order_amount_field() . ") AS turnover ";

        $sql = "SELECT u.user_id, u.user_name, COUNT(*) AS order_num, " . $total_fee .
            "FROM " . $this->ecs->table('users') . " AS u, " . $this->ecs->table('order_info') . " AS o " . $where .
            " GROUP BY u.user_id" . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
        if ($is_pagination) {
            $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
        }
        $user_orderinfo = array();
        $res = $this->db->query($sql);

        foreach ($res as $items) {
            $items['turnover'] = price_format($items['turnover']);
            $user_orderinfo[] = $items;
        }
        $arr = array('user_orderinfo' => $user_orderinfo, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
        return $arr;
    }
}