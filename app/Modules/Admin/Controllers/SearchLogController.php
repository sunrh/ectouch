<?php

namespace app\modules\admin\controllers;

/**
 * Class SearchLogController
 * @package app\modules\admin\controllers
 */
class SearchLogController extends Controller
{
    public function actionIndex()
    {

        admin_priv('search_log');
        if ($_REQUEST['act'] == 'list') {
            $logdb = $this->get_search_log();
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['search_log']);
            $this->smarty->assign('full_page', 1);
            $this->smarty->assign('logdb', $logdb['logdb']);
            $this->smarty->assign('filter', $logdb['filter']);
            $this->smarty->assign('record_count', $logdb['record_count']);
            $this->smarty->assign('page_count', $logdb['page_count']);
            $this->smarty->assign('start_date', local_date('Y-m-d'));
            $this->smarty->assign('end_date', local_date('Y-m-d'));

            $this->smarty->display('search_log_list.htm');
        }
        if ($_REQUEST['act'] == 'query') {
            $logdb = $this->get_search_log();
            $this->smarty->assign('full_page', 0);
            $this->smarty->assign('logdb', $logdb['logdb']);
            $this->smarty->assign('filter', $logdb['filter']);
            $this->smarty->assign('record_count', $logdb['record_count']);
            $this->smarty->assign('page_count', $logdb['page_count']);
            $this->smarty->assign('start_date', local_date('Y-m-d'));
            $this->smarty->assign('end_date', local_date('Y-m-d'));
            make_json_result($this->smarty->fetch('search_log_list.htm'), '',
                array('filter' => $logdb['filter'], 'page_count' => $logdb['page_count']));
        }
    }

    private function get_search_log()
    {
        $where = '';
        if (isset($_REQUEST['start_dateYear']) && isset($_REQUEST['end_dateYear'])) {
            $start_date = $_POST['start_dateYear'] . '-' . $_POST['start_dateMonth'] . '-' . $_POST['start_dateDay'];
            $end_date = $_POST['end_dateYear'] . '-' . $_POST['end_dateMonth'] . '-' . $_POST['end_dateDay'];
            $where .= " AND date <= '$end_date' AND date >= '$start_date'";
            $filter['start_dateYear'] = $_REQUEST['start_dateYear'];
            $filter['start_dateMonth'] = $_REQUEST['start_dateMonth'];
            $filter['start_dateDay'] = $_REQUEST['start_dateDay'];

            $filter['end_dateYear'] = $_REQUEST['end_dateYear'];
            $filter['end_dateMonth'] = $_REQUEST['end_dateMonth'];
            $filter['end_dateDay'] = $_REQUEST['end_dateDay'];
        }

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('keywords') . " WHERE  searchengine='ecshop' $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $logdb = array();
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('keywords') .
            " WHERE  searchengine='ecshop' $where" .
            " ORDER BY date DESC, count DESC" .
            "  LIMIT $filter[start],$filter[page_size]";
        $query = $GLOBALS['db']->query($sql);

        foreach ($query as $rt) {
            $logdb[] = $rt;
        }
        $arr = array('logdb' => $logdb, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }

}