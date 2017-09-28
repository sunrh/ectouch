<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class GoodsBookingController
 * @package App\Modules\Admin\Controllers
 */
class GoodsBookingController extends Controller
{
    public function actionIndex()
    {


        /**
         *  缺货处理管理程序
         */

        admin_priv('booking');
        /*------------------------------------------------------ */
//-- 列出所有订购信息
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list_all') {
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['list_all']);
            $this->smarty->assign('full_page', 1);

            $list = $this->get_bookinglist();

            $this->smarty->assign('booking_list', $list['item']);
            $this->smarty->assign('filter', $list['filter']);
            $this->smarty->assign('record_count', $list['record_count']);
            $this->smarty->assign('page_count', $list['page_count']);

            $sort_flag = sort_flag($list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);


            $this->smarty->display('booking_list.htm');
        }

        /*------------------------------------------------------ */
//-- 翻页、排序
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'query') {
            $list = $this->get_bookinglist();

            $this->smarty->assign('booking_list', $list['item']);
            $this->smarty->assign('filter', $list['filter']);
            $this->smarty->assign('record_count', $list['record_count']);
            $this->smarty->assign('page_count', $list['page_count']);

            $sort_flag = sort_flag($list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('booking_list.htm'), '',
                array('filter' => $list['filter'], 'page_count' => $list['page_count']));
        }

        /*------------------------------------------------------ */
//-- 删除缺货登记
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('booking');

            $id = intval($_GET['id']);

            $this->db->query("DELETE FROM " . $this->ecs->table('booking_goods') . " WHERE rec_id='$id'");

            $url = 'goods_booking.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

            ecs_header("Location: $url\n");
            exit;
        }

        /*------------------------------------------------------ */
//-- 显示详情
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'detail') {
            $id = intval($_REQUEST['id']);

            $this->smarty->assign('send_fail', !empty($_REQUEST['send_ok']));
            $this->smarty->assign('booking', $this->get_booking_info($id));
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['detail']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['06_undispose_booking'], 'href' => 'goods_booking.php?act=list_all'));
            $this->smarty->display('booking_info.htm');
        }

        /*------------------------------------------------------ */
//-- 处理提交数据
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'update') {
            /* 权限判断 */
            admin_priv('booking');

            $dispose_note = !empty($_POST['dispose_note']) ? trim($_POST['dispose_note']) : '';

            $sql = "UPDATE  " . $this->ecs->table('booking_goods') .
                " SET is_dispose='1', dispose_note='$dispose_note', " .
                "dispose_time='" . gmtime() . "', dispose_user='" . session('admin_name') . "'" .
                " WHERE rec_id='$_REQUEST[rec_id]'";
            $this->db->query($sql);

            /* 邮件通知处理流程 */
            if (!empty($_POST['send_email_notice']) or isset($_POST['remail'])) {
                //获取邮件中的必要内容
                $sql = 'SELECT bg.email, bg.link_man, bg.goods_id, g.goods_name ' .
                    'FROM ' . $this->ecs->table('booking_goods') . ' AS bg, ' . $this->ecs->table('goods') . ' AS g ' .
                    "WHERE bg.goods_id = g.goods_id AND bg.rec_id='$_REQUEST[rec_id]'";
                $booking_info = $this->db->getRow($sql);

                /* 设置缺货回复模板所需要的内容信息 */
                $template = get_mail_template('goods_booking');
                $goods_link = $this->ecs->url() . 'goods.php?id=' . $booking_info['goods_id'];

                $this->smarty->assign('user_name', $booking_info['link_man']);
                $this->smarty->assign('goods_link', $goods_link);
                $this->smarty->assign('goods_name', $booking_info['goods_name']);
                $this->smarty->assign('dispose_note', $dispose_note);
                $this->smarty->assign('shop_name', "<a href='" . $this->ecs->url() . "'>" . $GLOBALS['_CFG']['shop_name'] . '</a>');
                $this->smarty->assign('send_date', date('Y-m-d'));

                $content = $this->smarty->fetch('str:' . $template['template_content']);

                /* 发送邮件 */
                if (send_mail($booking_info['link_man'], $booking_info['email'], $template['template_subject'], $content, $template['is_html'])) {
                    $send_ok = 0;
                } else {
                    $send_ok = 1;
                }
            }

            ecs_header("Location: ?act=detail&id=" . $_REQUEST['rec_id'] . "&send_ok=$send_ok\n");
            exit;
        }
    }

    /**
     * 获取订购信息
     *
     * @access  public
     *
     * @return array
     */
    private function get_bookinglist()
    {
        /* 查询条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['dispose'] = empty($_REQUEST['dispose']) ? 0 : intval($_REQUEST['dispose']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'sort_order' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = (!empty($_REQUEST['keywords'])) ? " AND g.goods_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';
        $where .= (!empty($_REQUEST['dispose'])) ? " AND bg.is_dispose = '$filter[dispose]' " : '';

        $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('booking_goods') . ' AS bg, ' .
            $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "WHERE bg.goods_id = g.goods_id $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 获取活动数据 */
        $sql = 'SELECT bg.rec_id, bg.link_man, g.goods_id, g.goods_name, bg.goods_number, bg.booking_time, bg.is_dispose ' .
            'FROM ' . $GLOBALS['ecs']->table('booking_goods') . ' AS bg, ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "WHERE bg.goods_id = g.goods_id $where " .
            "ORDER BY $filter[sort_by] $filter[sort_order] " .
            "LIMIT " . $filter['start'] . ", $filter[page_size]";
        $row = $GLOBALS['db']->getAll($sql);

        foreach ($row as $key => $val) {
            $row[$key]['booking_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['booking_time']);
        }
        $filter['keywords'] = stripslashes($filter['keywords']);
        $arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }

    /**
     * 获得缺货登记的详细信息
     *
     * @param   integer $id
     *
     * @return  array
     */
    private function get_booking_info($id)
    {
        $sql = "SELECT bg.rec_id, bg.user_id, IFNULL(u.user_name, '{$GLOBALS['_LANG']['guest_user']}') AS user_name, " .
            "bg.link_man, g.goods_name, bg.goods_id, bg.goods_number, " .
            "bg.booking_time, bg.goods_desc,bg.dispose_user, bg.dispose_time, bg.email, " .
            "bg.tel, bg.dispose_note ,bg.dispose_user, bg.dispose_time,bg.is_dispose  " .
            "FROM " . $this->ecs->table('booking_goods') . " AS bg " .
            "LEFT JOIN " . $this->ecs->table('goods') . " AS g ON g.goods_id=bg.goods_id " .
            "LEFT JOIN " . $this->ecs->table('users') . " AS u ON u.user_id=bg.user_id " .
            "WHERE bg.rec_id ='$id'";

        $res = $this->db->GetRow($sql);

        /* 格式化时间 */
        $res['booking_time'] = local_date($GLOBALS['_CFG']['time_format'], $res['booking_time']);
        if (!empty($res['dispose_time'])) {
            $res['dispose_time'] = local_date($GLOBALS['_CFG']['time_format'], $res['dispose_time']);
        }

        return $res;
    }
}