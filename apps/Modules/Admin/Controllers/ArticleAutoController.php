<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class ArticleAutoController
 * @package App\Modules\Admin\Controllers
 */
class ArticleAutoController extends Controller
{
    public function actionIndex()
    {
        admin_priv('article_auto');
        $this->smarty->assign('thisfile', 'article_auto.php');
        if ($_REQUEST['act'] == 'list') {
            $goodsdb = $this->get_auto_goods();
            $crons_enable = $this->db->getOne("SELECT enable FROM " . $GLOBALS['ecs']->table('crons') . " WHERE cron_code='ipdel'");
            $this->smarty->assign('crons_enable', $crons_enable);
            $this->smarty->assign('full_page', 1);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['article_auto']);
            $this->smarty->assign('goodsdb', $goodsdb['goodsdb']);
            $this->smarty->assign('filter', $goodsdb['filter']);
            $this->smarty->assign('record_count', $goodsdb['record_count']);
            $this->smarty->assign('page_count', $goodsdb['page_count']);

            $this->smarty->display('goods_auto.htm');
        }
        if ($_REQUEST['act'] == 'query') {
            $goodsdb = $this->get_auto_goods();
            $this->smarty->assign('goodsdb', $goodsdb['goodsdb']);
            $this->smarty->assign('filter', $goodsdb['filter']);
            $this->smarty->assign('record_count', $goodsdb['record_count']);
            $this->smarty->assign('page_count', $goodsdb['page_count']);

            $sort_flag = sort_flag($goodsdb['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('goods_auto.htm'), '', array('filter' => $goodsdb['filter'], 'page_count' => $goodsdb['page_count']));
        }
        if ($_REQUEST['act'] == 'del') {
            $goods_id = (int)$_REQUEST['goods_id'];
            $sql = "DELETE FROM " . $this->ecs->table('auto_manage') . " WHERE item_id = '$goods_id' AND type = 'article'";
            $this->db->query($sql);
            $links[] = array('text' => $GLOBALS['_LANG']['article_auto'], 'href' => 'article_auto.php?act=list');
            sys_msg($GLOBALS['_LANG']['edit_ok'], 0, $links);
        }
        if ($_REQUEST['act'] == 'edit_starttime') {
            check_authz_json('goods_auto');

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($_POST['val']))) {
                make_json_error('');
            }

            $id = intval($_POST['id']);
            $time = local_strtotime(trim($_POST['val']));
            if ($id <= 0 || $_POST['val'] == '0000-00-00' || $time <= 0) {
                make_json_error('');
            }

            $this->db->autoReplace($this->ecs->table('auto_manage'), array('item_id' => $id, 'type' => 'article',
                'starttime' => $time), array('starttime' => (string)$time));

            clear_cache_files();
            make_json_result(stripslashes($_POST['val']), '', array('act' => 'article_auto', 'id' => $id));
        }
        if ($_REQUEST['act'] == 'edit_endtime') {
            check_authz_json('goods_auto');

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($_POST['val']))) {
                make_json_error('');
            }

            $id = intval($_POST['id']);
            $time = local_strtotime(trim($_POST['val']));
            if ($id <= 0 || $_POST['val'] == '0000-00-00' || $time <= 0) {
                make_json_error('');
            }

            $this->db->autoReplace($this->ecs->table('auto_manage'), array('item_id' => $id, 'type' => 'article',
                'endtime' => $time), array('endtime' => (string)$time));

            clear_cache_files();
            make_json_result(stripslashes($_POST['val']), '', array('act' => 'article_auto', 'id' => $id));
        }

//批量发布
        if ($_REQUEST['act'] == 'batch_start') {
            admin_priv('goods_auto');

            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes'])) {
                sys_msg($GLOBALS['_LANG']['no_select_goods'], 1);
            }

            if ($_POST['date'] == '0000-00-00') {
                $_POST['date'] = 0;
            } else {
                $_POST['date'] = local_strtotime(trim($_POST['date']));
            }

            foreach ($_POST['checkboxes'] as $id) {
                $this->db->autoReplace($this->ecs->table('auto_manage'), array('item_id' => $id, 'type' => 'article',
                    'starttime' => $_POST['date']), array('starttime' => (string)$_POST['date']));
            }

            $lnk[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'article_auto.php?act=list');
            sys_msg($GLOBALS['_LANG']['batch_start_succeed'], 0, $lnk);
        }

//批量取消发布
        if ($_REQUEST['act'] == 'batch_end') {
            admin_priv('goods_auto');

            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes'])) {
                sys_msg($GLOBALS['_LANG']['no_select_goods'], 1);
            }

            if ($_POST['date'] == '0000-00-00') {
                $_POST['date'] = 0;
            } else {
                $_POST['date'] = local_strtotime(trim($_POST['date']));
            }

            foreach ($_POST['checkboxes'] as $id) {
                $this->db->autoReplace($this->ecs->table('auto_manage'), array('item_id' => $id, 'type' => 'article',
                    'endtime' => $_POST['date']), array('endtime' => (string)$_POST['date']));
            }

            $lnk[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'article_auto.php?act=list');
            sys_msg($GLOBALS['_LANG']['batch_end_succeed'], 0, $lnk);
        }
    }

    private function get_auto_goods()
    {
        $where = '';
        if (!empty($_POST['goods_name'])) {
            $goods_name = trim($_POST['goods_name']);
            $where = " WHERE g.title LIKE '%$goods_name%'";
            $filter['goods_name'] = $goods_name;
        }
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('article') . " g" . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $goodsdb = array();
        $filter = page_and_size($filter);
        $sql = "SELECT g.*,a.starttime,a.endtime FROM " . $GLOBALS['ecs']->table('article') . " g LEFT JOIN " . $GLOBALS['ecs']->table('auto_manage') . " a ON g.article_id = a.item_id AND a.type='article'" . $where .
            " ORDER BY g. add_time DESC" .
            " LIMIT " . $filter['start'] . ",$filter[page_size]";
        $query = $GLOBALS['db']->query($sql);
        foreach ($query as $rt) {
            if (!empty($rt['starttime'])) {
                $rt['starttime'] = local_date('Y-m-d', $rt['starttime']);
            }
            if (!empty($rt['endtime'])) {
                $rt['endtime'] = local_date('Y-m-d', $rt['endtime']);
            }
            $rt['goods_id'] = $rt['article_id'];
            $rt['goods_name'] = $rt['title'];
            $goodsdb[] = $rt;
        }
        $arr = array('goodsdb' => $goodsdb, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }
}