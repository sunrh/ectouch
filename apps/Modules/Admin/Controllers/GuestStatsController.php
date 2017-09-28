<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class GuestStatsController
 * @package App\Modules\Admin\Controllers
 */
class GuestStatsController extends Controller
{
    public function actionIndex()
    {


        /**
         *  客户统计
         */
        load_helper('order');
        load_lang('statistic','admin');

        /*------------------------------------------------------ */
//-- 客户统计列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            /* 权限判断 */
            admin_priv('client_flow_stats');

            /* 取得会员总数 */
            $users =& init_users();
            $sql = "SELECT COUNT(*) FROM " . $this->ecs->table("users");
            $res = $this->db->getCol($sql);
            $user_num = $res[0];


            /* 计算订单各种费用之和的语句 */
            $total_fee = " SUM(" . order_amount_field() . ") AS turnover ";

            /* 有过订单的会员数 */
            $sql = 'SELECT COUNT(DISTINCT user_id) FROM ' . $this->ecs->table('order_info') .
                " WHERE user_id > 0 " . order_query_sql('finished');
            $have_order_usernum = $this->db->getOne($sql);

            /* 会员订单总数和订单总购物额 */
            $user_all_order = array();
            $sql = "SELECT COUNT(*) AS order_num, " . $total_fee .
                "FROM " . $this->ecs->table('order_info') .
                " WHERE user_id > 0 " . order_query_sql('finished');
            $user_all_order = $this->db->getRow($sql);
            $user_all_order['turnover'] = floatval($user_all_order['turnover']);

            /* 匿名会员订单总数和总购物额 */
            $guest_all_order = array();
            $sql = "SELECT COUNT(*) AS order_num, " . $total_fee .
                "FROM " . $this->ecs->table('order_info') .
                " WHERE user_id = 0 " . order_query_sql('finished');
            $guest_all_order = $this->db->getRow($sql);

            /* 匿名会员平均订单额: 购物总额/订单数 */
            $guest_order_amount = ($guest_all_order['order_num'] > 0) ? floatval($guest_all_order['turnover'] / $guest_all_order['order_num']) : '0.00';

            $_GET['flag'] = isset($_GET['flag']) ? 'download' : '';
            if ($_GET['flag'] == 'download') {
                $filename = ecs_iconv(CHARSET, 'GB2312', $GLOBALS['_LANG']['guest_statistics']);

                header("Content-type: application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=$filename.xls");

                /* 生成会员购买率 */
                $data = $GLOBALS['_LANG']['percent_buy_member'] . "\t\n";
                $data .= $GLOBALS['_LANG']['member_count'] . "\t" . $GLOBALS['_LANG']['order_member_count'] . "\t" .
                    $GLOBALS['_LANG']['member_order_count'] . "\t" . $GLOBALS['_LANG']['percent_buy_member'] . "\n";

                $data .= $user_num . "\t" . $have_order_usernum . "\t" .
                    $user_all_order['order_num'] . "\t" . sprintf("%0.2f", ($user_num > 0 ? $have_order_usernum / $user_num : 0) * 100) . "\n\n";

                /* 每会员平均订单数及购物额 */
                $data .= $GLOBALS['_LANG']['order_turnover_peruser'] . "\t\n";

                $data .= $GLOBALS['_LANG']['member_sum'] . "\t" . $GLOBALS['_LANG']['average_member_order'] . "\t" .
                    $GLOBALS['_LANG']['member_order_sum'] . "\n";

                $ave_user_ordernum = $user_num > 0 ? sprintf("%0.2f", $user_all_order['order_num'] / $user_num) : 0;
                $ave_user_turnover = $user_num > 0 ? price_format($user_all_order['turnover'] / $user_num) : 0;

                $data .= price_format($user_all_order['turnover']) . "\t" . $ave_user_ordernum . "\t" . $ave_user_turnover . "\n\n";

                /* 每会员平均订单数及购物额 */
                $data .= $GLOBALS['_LANG']['order_turnover_percus'] . "\t\n";
                $data .= $GLOBALS['_LANG']['guest_member_orderamount'] . "\t" . $GLOBALS['_LANG']['guest_member_ordercount'] . "\t" .
                    $GLOBALS['_LANG']['guest_order_sum'] . "\n";

                $order_num = $guest_all_order['order_num'] > 0 ? price_format($guest_all_order['turnover'] / $guest_all_order['order_num']) : 0;
                $data .= price_format($guest_all_order['turnover']) . "\t" . $guest_all_order['order_num'] . "\t" .
                    $order_num;

                echo ecs_iconv(CHARSET, 'GB2312', $data) . "\t";
                exit;
            }

            /* 赋值到模板 */
            $this->smarty->assign('user_num', $user_num);                    // 会员总数
            $this->smarty->assign('have_order_usernum', $have_order_usernum);          // 有过订单的会员数
            $this->smarty->assign('user_order_turnover', $user_all_order['order_num']); // 会员总订单数
            $this->smarty->assign('user_all_turnover', price_format($user_all_order['turnover']));  //会员购物总额
            $this->smarty->assign('guest_all_turnover', price_format($guest_all_order['turnover'])); //匿名会员购物总额
            $this->smarty->assign('guest_order_num', $guest_all_order['order_num']);              //匿名会员订单总数

            /* 每会员订单数 */
            $this->smarty->assign('ave_user_ordernum', $user_num > 0 ? sprintf("%0.2f", $user_all_order['order_num'] / $user_num) : 0);

            /* 每会员购物额 */
            $this->smarty->assign('ave_user_turnover', $user_num > 0 ? price_format($user_all_order['turnover'] / $user_num) : 0);

            /* 注册会员购买率 */
            $this->smarty->assign('user_ratio', sprintf("%0.2f", ($user_num > 0 ? $have_order_usernum / $user_num : 0) * 100));

            /* 匿名会员平均订单额 */
            $this->smarty->assign('guest_order_amount', $guest_all_order['order_num'] > 0 ? price_format($guest_all_order['turnover'] / $guest_all_order['order_num']) : 0);

            $this->smarty->assign('all_order', $user_all_order);    //所有订单总数以及所有购物总额
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['report_guest']);
            $this->smarty->assign('lang', $GLOBALS['_LANG']);

            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['down_guest_stats'],
                'href' => 'guest_stats.php?flag=download'));


            $this->smarty->display('guest_stats.htm');
        }
    }
}