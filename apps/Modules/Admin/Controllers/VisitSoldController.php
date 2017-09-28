<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class VisitSoldController
 * @package App\Modules\Admin\Controllers
 */
class VisitSoldController extends Controller
{
    public function actionIndex()
    {

        /**
         *  访问购买比例
         */
        load_helper('order');
        load_lang('statistic', 'admin');

        $this->smarty->assign('lang', $GLOBALS['_LANG']);

        /* act操作项的初始化 */
        if (empty($_REQUEST['act'])) {
            $_REQUEST['act'] = 'list';
        } else {
            $_REQUEST['act'] = trim($_REQUEST['act']);
        }

        admin_priv('client_flow_stats');

        /*------------------------------------------------------ */
//--访问购买比例
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'download') {
            /* 变量的初始化 */
            $cat_id = (!empty($_REQUEST['cat_id'])) ? intval($_REQUEST['cat_id']) : 0;
            $brand_id = (!empty($_REQUEST['brand_id'])) ? intval($_REQUEST['brand_id']) : 0;
            $show_num = (!empty($_REQUEST['show_num'])) ? intval($_REQUEST['show_num']) : 15;

            /* 获取访问购买的比例数据 */
            $click_sold_info = $this->click_sold_info($cat_id, $brand_id, $show_num);

            /* 下载报表 */
            if ($_REQUEST['act'] == "download") {
                $filename = 'visit_sold';
                header("Content-type: application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=$filename.xls");
                $data = "{$GLOBALS['_LANG'][visit_buy]}\t\n";
                $data .= "{$GLOBALS['_LANG'][order_by]}\t{$GLOBALS['_LANG'][goods_name]}\t{$GLOBALS['_LANG'][fav_exponential]}\t{$GLOBALS['_LANG'][buy_times]}\t{$GLOBALS['_LANG'][visit_buy]}\n";
                foreach ($click_sold_info AS $k => $row) {
                    $order_by = $k + 1;
                    $data .= "$order_by\t$row[goods_name]\t$row[click_count]\t$row[sold_times]\t$row[scale]\n";
                }
                echo ecs_iconv(CHARSET, 'GB2312', $data);
                exit;
            }

            /* 赋值到模板 */
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['visit_buy_per']);

            $this->smarty->assign('show_num', $show_num);
            $this->smarty->assign('brand_id', $brand_id);
            $this->smarty->assign('click_sold_info', $click_sold_info);

            $this->smarty->assign('cat_list', cat_list(0, $cat_id));
            $this->smarty->assign('brand_list', get_brand_list());

            $filename = 'visit_sold';
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['download_visit_buy'], 'href' => 'visit_sold.php?act=download&show_num=' . $show_num . '&cat_id=' . $cat_id . '&brand_id=' . $brand_id . '&show_num=' . $show_num));

            /* 显示页面 */

            $this->smarty->display('visit_sold.htm');
        }
    }
    /*------------------------------------------------------ */
//--订单统计需要的函数
    /*------------------------------------------------------ */
    /**
     * 取得访问和购买次数统计数据
     *
     * @param   int $cat_id 分类编号
     * @param   int $brand_id 品牌编号
     * @param   int $show_num 显示个数
     * @return  array           $click_sold_info  访问购买比例数据
     */
    private function click_sold_info($cat_id, $brand_id, $show_num)
    {

        $where = " WHERE o.order_id = og.order_id AND g.goods_id = og.goods_id " . order_query_sql('finished', 'o.');
        $limit = " LIMIT " . $show_num;

        if ($cat_id > 0) {
            $where .= " AND " . get_children($cat_id);
        }
        if ($brand_id > 0) {
            $where .= " AND g.brand_id = '$brand_id' ";
        }

        $click_sold_info = array();
        $sql = "SELECT og.goods_id, g.goods_sn, g.goods_name, g.click_count,  COUNT(og.goods_id) AS sold_times " .
            " FROM " . $this->ecs->table('goods') . " AS g, " . $this->ecs->table('order_goods') . " AS og, " . $this->ecs->table('order_info') . " AS o " . $where .
            " GROUP BY og.goods_id ORDER BY g.click_count DESC " . $limit;
        $res = $this->db->query($sql);

        foreach ($res as $item) {
            if ($item['click_count'] <= 0) {
                $item['scale'] = 0;
            } else {
                /* 每一百个点击的订单比率 */
                $item['scale'] = sprintf("%0.2f", ($item['sold_times'] / $item['click_count']) * 100) . '%';
            }

            $click_sold_info[] = $item;
        }

        return $click_sold_info;
    }
}