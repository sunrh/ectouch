<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Exchange;

/**
 * Class PackageController
 * @package App\Modules\Admin\Controllers
 */
class PackageController extends Controller
{
    public function actionIndex()
    {


        /**
         *  超值礼包管理程序
         */

        $exc = new Exchange($this->ecs->table("goods_activity"), $this->db, 'act_id', 'act_name');

        /*------------------------------------------------------ */
//-- 添加活动
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'add') {
            /* 权限判断 */
            admin_priv('package_manage');

            /* 组合商品 */
            $group_goods_list = array();
            $sql = "DELETE FROM " . $this->ecs->table('package_goods') .
                " WHERE package_id = 0 AND admin_id = '" . session('admin_id') . "'";

            $this->db->query($sql);

            /* 初始化信息 */
            $start_time = local_date('Y-m-d H:i');
            $end_time = local_date('Y-m-d H:i', strtotime('+1 month'));
            $package = array('package_price' => '', 'start_time' => $start_time, 'end_time' => $end_time);

            $this->smarty->assign('package', $package);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['package_add']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['14_package_list'], 'href' => 'package.php?act=list'));
            $this->smarty->assign('cat_list', cat_list());
            $this->smarty->assign('brand_list', get_brand_list());
            $this->smarty->assign('form_action', 'insert');


            $this->smarty->display('package_info.htm');
        }
        if ($_REQUEST['act'] == 'insert') {
            /* 权限判断 */
            admin_priv('package_manage');

            $sql = "SELECT COUNT(*) " .
                " FROM " . $this->ecs->table('goods_activity') .
                " WHERE act_type='" . GAT_PACKAGE . "' AND act_name='" . $_POST['package_name'] . "'";
            if ($this->db->getOne($sql)) {
                sys_msg(sprintf($GLOBALS['_LANG']['package_exist'], $_POST['package_name']), 1);
            }


            /* 将时间转换成整数 */
            $_POST['start_time'] = local_strtotime($_POST['start_time']);
            $_POST['end_time'] = local_strtotime($_POST['end_time']);

            /* 处理提交数据 */
            if (empty($_POST['package_price'])) {
                $_POST['package_price'] = 0;
            }

            $info = array('package_price' => $_POST['package_price']);

            /* 插入数据 */
            $record = array('act_name' => $_POST['package_name'], 'act_desc' => $_POST['desc'],
                'act_type' => GAT_PACKAGE, 'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'], 'is_finished' => 0, 'ext_info' => serialize($info));

            $this->db->AutoExecute($this->ecs->table('goods_activity'), $record, 'INSERT');

            /* 礼包编号 */
            $package_id = $this->db->insert_id();

            $this->handle_packagep_goods($package_id);

            admin_log($_POST['package_name'], 'add', 'package');
            $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'package.php?act=list');
            $link[] = array('text' => $GLOBALS['_LANG']['continue_add'], 'href' => 'package.php?act=add');
            sys_msg($GLOBALS['_LANG']['add_succeed'], 0, $link);
        }

        /*------------------------------------------------------ */
//-- 编辑活动
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit') {
            /* 权限判断 */
            admin_priv('package_manage');

            $package = get_package_info($_REQUEST['id']);
            $package_goods_list = get_package_goods($_REQUEST['id']); // 礼包商品

            $this->smarty->assign('package', $package);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['package_edit']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['14_package_list'], 'href' => 'package.php?act=list&' . list_link_postfix()));
            $this->smarty->assign('cat_list', cat_list());
            $this->smarty->assign('brand_list', get_brand_list());
            $this->smarty->assign('form_action', 'update');
            $this->smarty->assign('package_goods_list', $package_goods_list);


            $this->smarty->display('package_info.htm');
        }
        if ($_REQUEST['act'] == 'update') {
            /* 权限判断 */
            admin_priv('package_manage');

            /* 将时间转换成整数 */
            $_POST['start_time'] = local_strtotime($_POST['start_time']);
            $_POST['end_time'] = local_strtotime($_POST['end_time']);

            /* 处理提交数据 */
            if (empty($_POST['package_price'])) {
                $_POST['package_price'] = 0;
            }

            /* 检查活动重名 */
            $sql = "SELECT COUNT(*) " .
                " FROM " . $this->ecs->table('goods_activity') .
                " WHERE act_type='" . GAT_PACKAGE . "' AND act_name='" . $_POST['package_name'] . "' AND act_id <> '" . $_POST['id'] . "'";
            if ($this->db->getOne($sql)) {
                sys_msg(sprintf($GLOBALS['_LANG']['package_exist'], $_POST['package_name']), 1);
            }


            $info = array('package_price' => $_POST['package_price']);

            /* 更新数据 */
            $record = array('act_name' => $_POST['package_name'], 'start_time' => $_POST['start_time'], 'end_time' => $_POST['end_time'],
                'act_desc' => $_POST['desc'], 'ext_info' => serialize($info));
            $this->db->autoExecute($this->ecs->table('goods_activity'), $record, 'UPDATE', "act_id = '" . $_POST['id'] . "' AND act_type = " . GAT_PACKAGE);

            admin_log($_POST['package_name'], 'edit', 'package');
            $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'package.php?act=list&' . list_link_postfix());
            sys_msg($GLOBALS['_LANG']['edit_succeed'], 0, $link);
        }

        /*------------------------------------------------------ */
//-- 删除指定的活动
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('package_manage');

            $id = intval($_GET['id']);

            $exc->drop($id);

            $sql = "DELETE FROM " . $this->ecs->table('package_goods') .
                " WHERE package_id='$id'";
            $this->db->query($sql);

            $url = 'package.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

            ecs_header("Location: $url\n");
            exit;
        }

        /*------------------------------------------------------ */
//-- 活动列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['14_package_list']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['package_add'], 'href' => 'package.php?act=add'));

            $packages = $this->get_packagelist();

            $this->smarty->assign('package_list', $packages['packages']);
            $this->smarty->assign('filter', $packages['filter']);
            $this->smarty->assign('record_count', $packages['record_count']);
            $this->smarty->assign('page_count', $packages['page_count']);

            $sort_flag = sort_flag($packages['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            $this->smarty->assign('full_page', 1);

            $this->smarty->display('package_list.htm');
        }

        /*------------------------------------------------------ */
//-- 查询、翻页、排序
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'query') {
            $packages = $this->get_packagelist();

            $this->smarty->assign('package_list', $packages['packages']);
            $this->smarty->assign('filter', $packages['filter']);
            $this->smarty->assign('record_count', $packages['record_count']);
            $this->smarty->assign('page_count', $packages['page_count']);

            $sort_flag = sort_flag($packages['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('package_list.htm'), '',
                array('filter' => $packages['filter'], 'page_count' => $packages['page_count']));
        }

        /*------------------------------------------------------ */
//-- 编辑活动名称
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'edit_package_name') {
            check_authz_json('package_manage');

            $id = intval($_POST['id']);
            $val = json_str_iconv(trim($_POST['val']));

            /* 检查活动重名 */
            $sql = "SELECT COUNT(*) " .
                " FROM " . $this->ecs->table('goods_activity') .
                " WHERE act_type='" . GAT_PACKAGE . "' AND act_name='$val' AND act_id <> '$id'";
            if ($this->db->getOne($sql)) {
                make_json_error(sprintf($GLOBALS['_LANG']['package_exist'], $val));
            }

            $exc->edit("act_name='$val'", $id);
            make_json_result(stripslashes($val));
        }

        /*------------------------------------------------------ */
//-- 搜索商品
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'search_goods') {
            // include_once(ROOT_PATH . 'includes/cls_json.php');
            $json = new Json();

            $filters = $json->decode($_GET['JSON']);

            $arr = get_goods_list($filters);

            $opt = array();
            foreach ($arr as $key => $val) {
                $opt[$key] = array('value' => $val['goods_id'],
                    'text' => $val['goods_name'],
                    'data' => $val['shop_price']);

                $opt[$key]['products'] = get_good_products($val['goods_id']);
            }

            make_json_result($opt);
        }

        /*------------------------------------------------------ */
//-- 搜索商品，仅返回名称及ID
        /*------------------------------------------------------ */

//if ($_REQUEST['act'] == 'get_goods_list')
//{
//    // include_once(ROOT_PATH . 'includes/cls_json.php');
//    $json = new Json();
//
//    $filters = $json->decode($_GET['JSON']);
//
//    $arr = get_goods_list($filters);
//
//    $opt = array();
//    foreach ($arr AS $key => $val)
//    {
//        $opt[$key] = array('value' => $val['goods_id'],
//                        'text' => $val['goods_name'],
//                        'data' => $val['shop_price']);
//
//        $opt[$key]['products'] = get_good_products($val['goods_id']);
//    }
//
//    make_json_result($opt);
//}

        /*------------------------------------------------------ */
//-- 增加一个商品
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'add_package_goods') {
            // include_once(ROOT_PATH . 'includes/cls_json.php');
            $json = new Json();

            check_authz_json('package_manage');

            $fittings = $json->decode($_GET['add_ids']);
            $arguments = $json->decode($_GET['JSON']);
            $package_id = $arguments[0];
            $number = $arguments[1];

            foreach ($fittings as $val) {
                $val_array = explode('_', $val);
                if (!isset($val_array[1]) || $val_array[1] <= 0) {
                    $val_array[1] = 0;
                }

                $sql = "INSERT INTO " . $this->ecs->table('package_goods') . " (package_id, goods_id, product_id, goods_number, admin_id) " .
                    "VALUES ('$package_id', '" . $val_array[0] . "', '" . $val_array[1] . "', '$number', '" . session('admin_id') . "')";
                $this->db->query($sql, 'SILENT');
            }

            $arr = get_package_goods($package_id);
            $opt = array();

            foreach ($arr as $val) {
                $opt[] = array('value' => $val['g_p'],
                    'text' => $val['goods_name'],
                    'data' => '');
            }

            clear_cache_files();
            make_json_result($opt);
        }

        /*------------------------------------------------------ */
//-- 删除一个商品
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'drop_package_goods') {
            // include_once(ROOT_PATH . 'includes/cls_json.php');
            $json = new Json();

            check_authz_json('package_manage');

            $fittings = $json->decode($_GET['drop_ids']);
            $arguments = $json->decode($_GET['JSON']);
            $package_id = $arguments[0];

            $goods = array();
            $g_p = array();
            foreach ($fittings as $val) {
                $val_array = explode('_', $val);
                if (isset($val_array[1]) && $val_array[1] > 0) {
                    $g_p['product_id'][] = $val_array[1];
                    $g_p['goods_id'][] = $val_array[0];
                } else {
                    $goods[] = $val_array[0];
                }
            }

            if (!empty($goods)) {
                $sql = "DELETE FROM " . $this->ecs->table('package_goods') .
                    " WHERE package_id='$package_id' AND " . db_create_in($goods, 'goods_id');
                if ($package_id == 0) {
                    $sql .= " AND admin_id = '" . session('admin_id') . "'";
                }
                $this->db->query($sql);
            }

            if (!empty($g_p)) {
                $sql = "DELETE FROM " . $this->ecs->table('package_goods') .
                    " WHERE package_id='$package_id' AND " . db_create_in($g_p['goods_id'], 'goods_id') . " AND " . db_create_in($g_p['product_id'], 'product_id');
                if ($package_id == 0) {
                    $sql .= " AND admin_id = '" . session('admin_id') . "'";
                }
                $this->db->query($sql);
            }

            $arr = get_package_goods($package_id);
            $opt = array();

            foreach ($arr as $val) {
                $opt[] = array('value' => $val['goods_id'],
                    'text' => $val['goods_name'],
                    'data' => '');
            }

            clear_cache_files();
            make_json_result($opt);
        }
    }

    /**
     * 获取活动列表
     *
     * @access  public
     *
     * @return void
     */
    private function get_packagelist()
    {
        $result = get_filter();
        if ($result === false) {
            /* 查询条件 */
            $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
            if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
                $filter['keywords'] = json_str_iconv($filter['keywords']);
            }
            $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'act_id' : trim($_REQUEST['sort_by']);
            $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

            $where = (!empty($filter['keywords'])) ? " AND act_name like '%" . mysql_like_quote($filter['keywords']) . "%'" : '';

            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods_activity') .
                " WHERE act_type =" . GAT_PACKAGE . $where;
            $filter['record_count'] = $GLOBALS['db']->getOne($sql);

            $filter = page_and_size($filter);

            /* 获活动数据 */
            $sql = "SELECT act_id, act_name AS package_name, start_time, end_time, is_finished, ext_info " .
                " FROM " . $GLOBALS['ecs']->table('goods_activity') .
                " WHERE act_type = " . GAT_PACKAGE . $where .
                " ORDER by $filter[sort_by] $filter[sort_order] LIMIT " . $filter['start'] . ", " . $filter['page_size'];

            $filter['keywords'] = stripslashes($filter['keywords']);
            set_filter($filter, $sql);
        } else {
            $sql = $result['sql'];
            $filter = $result['filter'];
        }

        $row = $GLOBALS['db']->getAll($sql);

        foreach ($row as $key => $val) {
            $row[$key]['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['start_time']);
            $row[$key]['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['end_time']);
            $info = unserialize($row[$key]['ext_info']);
            unset($row[$key]['ext_info']);
            if ($info) {
                foreach ($info as $info_key => $info_val) {
                    $row[$key][$info_key] = $info_val;
                }
            }
        }

        $arr = array('packages' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }

    /**
     * 保存某礼包的商品
     * @param   int $package_id
     * @return  void
     */
    private function handle_packagep_goods($package_id)
    {
        $sql = "UPDATE " . $GLOBALS['ecs']->table('package_goods') . " SET " .
            " package_id = '$package_id' " .
            " WHERE package_id = '0'" .
            " AND admin_id = '" . session('admin_id') . "'";
        $GLOBALS['db']->query($sql);
    }
}