<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class SaleGeneralController
 * @package App\Modules\Admin\Controllers
 */
class SaleGeneralController extends Controller
{
    public function actionIndex()
    {

        /**
         *  销售概况
         */
        load_lang('statistic','admin');

        $this->smarty->assign('lang', $GLOBALS['_LANG']);

        /* 权限判断 */
        admin_priv('sale_order_stats');

        /* act操作项的初始化 */
        if (empty($_REQUEST['act']) || !in_array($_REQUEST['act'], array('list', 'download'))) {
            $_REQUEST['act'] = 'list';
        }

        /* 取得查询类型和查询时间段 */
        if (empty($_POST['query_by_year']) && empty($_POST['query_by_month'])) {
            if (empty($_GET['query_type'])) {
                /* 默认当年的月走势 */
                $query_type = 'month';
                $start_time = local_mktime(0, 0, 0, 1, 1, intval(date('Y')));
                $end_time = gmtime();
            } else {
                /* 下载时的参数 */
                $query_type = $_GET['query_type'];
                $start_time = $_GET['start_time'];
                $end_time = $_GET['end_time'];
            }
        } else {
            if (isset($_POST['query_by_year'])) {
                /* 年走势 */
                $query_type = 'year';
                $start_time = local_mktime(0, 0, 0, 1, 1, intval($_POST['year_beginYear']));
                $end_time = local_mktime(23, 59, 59, 12, 31, intval($_POST['year_endYear']));
            } else {
                /* 月走势 */
                $query_type = 'month';
                $start_time = local_mktime(0, 0, 0, intval($_POST['month_beginMonth']), 1, intval($_POST['month_beginYear']));
                $end_time = local_mktime(23, 59, 59, intval($_POST['month_endMonth']), 1, intval($_POST['month_endYear']));
                $end_time = local_mktime(23, 59, 59, intval($_POST['month_endMonth']), date('t', $end_time), intval($_POST['month_endYear']));
            }
        }

        /* 分组统计订单数和销售额：已发货时间为准 */
        $format = ($query_type == 'year') ? '%Y' : '%Y-%m';
        $sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(shipping_time), '$format') AS period, COUNT(*) AS order_count, " .
            "SUM(goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee - discount) AS order_amount " .
            "FROM " . $this->ecs->table('order_info') .
            " WHERE (order_status = '" . OS_CONFIRMED . "' OR order_status >= '" . OS_SPLITED . "')" .
            " AND (pay_status = '" . PS_PAYED . "' OR pay_status = '" . PS_PAYING . "') " .
            " AND (shipping_status = '" . SS_SHIPPED . "' OR shipping_status = '" . SS_RECEIVED . "') " .
            " AND shipping_time >= '$start_time' AND shipping_time <= '$end_time'" .
            " GROUP BY period ";
        $data_list = $this->db->getAll($sql);

        /*------------------------------------------------------ */
//-- 显示统计信息
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            /* 赋值查询时间段 */
            $this->smarty->assign('start_time', local_date('Y-m-d', $start_time));
            $this->smarty->assign('end_time', local_date('Y-m-d', $end_time));

            /* 赋值统计数据 */
            $xml = "<chart caption='' xAxisName='%s' showValues='0' decimals='0' formatNumberScale='0'>%s</chart>";
            $set = "<set label='%s' value='%s' />";
            $i = 0;
            $data_count = '';
            $data_amount = '';
            foreach ($data_list as $data) {
                $data_count .= sprintf($set, $data['period'], $data['order_count'], chart_color($i));
                $data_amount .= sprintf($set, $data['period'], $data['order_amount'], chart_color($i));
                $i++;
            }

            $this->smarty->assign('data_count', sprintf($xml, '', $data_count)); // 订单数统计数据
            $this->smarty->assign('data_amount', sprintf($xml, '', $data_amount));    // 销售额统计数据

            $this->smarty->assign('data_count_name', $GLOBALS['_LANG']['order_count_trend']);
            $this->smarty->assign('data_amount_name', $GLOBALS['_LANG']['order_amount_trend']);

            /* 根据查询类型生成文件名 */
            if ($query_type == 'year') {
                $filename = date('Y', $start_time) . "_" . date('Y', $end_time) . '_report';
            } else {
                $filename = date('Ym', $start_time) . "_" . date('Ym', $end_time) . '_report';
            }
            $this->smarty->assign('action_link',
                array('text' => $GLOBALS['_LANG']['down_sales_stats'],
                    'href' => 'sale_general.php?act=download&filename=' . $filename .
                        '&query_type=' . $query_type . '&start_time=' . $start_time . '&end_time=' . $end_time));

            /* 显示模板 */
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['report_sell']);

            $this->smarty->display('sale_general.htm');
        }

        /*------------------------------------------------------ */
//-- 下载EXCEL报表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'download') {
            /* 文件名 */
            $filename = !empty($_REQUEST['filename']) ? trim($_REQUEST['filename']) : '';

            header("Content-type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=$filename.xls");

            /* 文件标题 */
            echo ecs_iconv(CHARSET, 'GB2312', $filename . $GLOBALS['_LANG']['sales_statistics']) . "\t\n";

            /* 订单数量, 销售出商品数量, 销售金额 */
            echo ecs_iconv(CHARSET, 'GB2312', $GLOBALS['_LANG']['period']) . "\t";
            echo ecs_iconv(CHARSET, 'GB2312', $GLOBALS['_LANG']['order_count_trend']) . "\t";
            echo ecs_iconv(CHARSET, 'GB2312', $GLOBALS['_LANG']['order_amount_trend']) . "\t\n";

            foreach ($data_list as $data) {
                echo ecs_iconv(CHARSET, 'GB2312', $data['period']) . "\t";
                echo ecs_iconv(CHARSET, 'GB2312', $data['order_count']) . "\t";
                echo ecs_iconv(CHARSET, 'GB2312', $data['order_amount']) . "\t";
                echo "\n";
            }
        }
    }
}