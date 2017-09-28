<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class AgencyController
 * @package App\Modules\Admin\Controllers
 */
class AgencyController extends Controller
{
    public function actionIndex()
    {


        /**
         *  管理中心办事处管理
         */


        $exc = new exchange($this->ecs->table('agency'), $this->db, 'agency_id', 'agency_name');

        /*------------------------------------------------------ */
//-- 办事处列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['agency_list']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['add_agency'], 'href' => 'agency.php?act=add'));
            $this->smarty->assign('full_page', 1);

            $agency_list = $this->get_agencylist();
            $this->smarty->assign('agency_list', $agency_list['agency']);
            $this->smarty->assign('filter', $agency_list['filter']);
            $this->smarty->assign('record_count', $agency_list['record_count']);
            $this->smarty->assign('page_count', $agency_list['page_count']);

            /* 排序标记 */
            $sort_flag = sort_flag($agency_list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            $this->smarty->display('agency_list.htm');
        }

        /*------------------------------------------------------ */
//-- 排序、分页、查询
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'query') {
            $agency_list = $this->get_agencylist();
            $this->smarty->assign('agency_list', $agency_list['agency']);
            $this->smarty->assign('filter', $agency_list['filter']);
            $this->smarty->assign('record_count', $agency_list['record_count']);
            $this->smarty->assign('page_count', $agency_list['page_count']);

            /* 排序标记 */
            $sort_flag = sort_flag($agency_list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('agency_list.htm'), '',
                array('filter' => $agency_list['filter'], 'page_count' => $agency_list['page_count']));
        }

        /*------------------------------------------------------ */
//-- 列表页编辑名称
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit_agency_name') {
            check_authz_json('agency_manage');

            $id = intval($_POST['id']);
            $name = json_str_iconv(trim($_POST['val']));

            /* 检查名称是否重复 */
            if ($exc->num("agency_name", $name, $id) != 0) {
                make_json_error(sprintf($GLOBALS['_LANG']['agency_name_exist'], $name));
            } else {
                if ($exc->edit("agency_name = '$name'", $id)) {
                    admin_log($name, 'edit', 'agency');
                    clear_cache_files();
                    make_json_result(stripslashes($name));
                } else {
                    make_json_result(sprintf($GLOBALS['_LANG']['agency_edit_fail'], $name));
                }
            }
        }

        /*------------------------------------------------------ */
//-- 删除办事处
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('agency_manage');

            $id = intval($_GET['id']);
            $name = $exc->get_name($id);
            $exc->drop($id);

            /* 更新管理员、配送地区、发货单、退货单和订单关联的办事处 */
            $table_array = array('admin_user', 'region', 'order_info', 'delivery_order', 'back_order');
            foreach ($table_array as $value) {
                $sql = "UPDATE " . $this->ecs->table($value) . " SET agency_id = 0 WHERE agency_id = '$id'";
                $this->db->query($sql);
            }

            /* 记日志 */
            admin_log($name, 'remove', 'agency');

            /* 清除缓存 */
            clear_cache_files();

            $url = 'agency.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

            ecs_header("Location: $url\n");
            exit;
        }

        /*------------------------------------------------------ */
//-- 批量操作
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'batch') {
            /* 取得要操作的记录编号 */
            if (empty($_POST['checkboxes'])) {
                sys_msg($GLOBALS['_LANG']['no_record_selected']);
            } else {
                /* 检查权限 */
                admin_priv('agency_manage');

                $ids = $_POST['checkboxes'];

                if (isset($_POST['remove'])) {
                    /* 删除记录 */
                    $sql = "DELETE FROM " . $this->ecs->table('agency') .
                        " WHERE agency_id " . db_create_in($ids);
                    $this->db->query($sql);

                    /* 更新管理员、配送地区、发货单、退货单和订单关联的办事处 */
                    $table_array = array('admin_user', 'region', 'order_info', 'delivery_order', 'back_order');
                    foreach ($table_array as $value) {
                        $sql = "UPDATE " . $this->ecs->table($value) . " SET agency_id = 0 WHERE agency_id " . db_create_in($ids) . " ";
                        $this->db->query($sql);
                    }

                    /* 记日志 */
                    admin_log('', 'batch_remove', 'agency');

                    /* 清除缓存 */
                    clear_cache_files();

                    sys_msg($GLOBALS['_LANG']['batch_drop_ok']);
                }
            }
        }

        /*------------------------------------------------------ */
//-- 添加、编辑办事处
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
            /* 检查权限 */
            admin_priv('agency_manage');

            /* 是否添加 */
            $is_add = $_REQUEST['act'] == 'add';
            $this->smarty->assign('form_action', $is_add ? 'insert' : 'update');

            /* 初始化、取得办事处信息 */
            if ($is_add) {
                $agency = array(
                    'agency_id' => 0,
                    'agency_name' => '',
                    'agency_desc' => '',
                    'region_list' => array()
                );
            } else {
                if (empty($_GET['id'])) {
                    sys_msg('invalid param');
                }

                $id = $_GET['id'];
                $sql = "SELECT * FROM " . $this->ecs->table('agency') . " WHERE agency_id = '$id'";
                $agency = $this->db->getRow($sql);
                if (empty($agency)) {
                    sys_msg('agency does not exist');
                }

                /* 关联的地区 */
                $sql = "SELECT region_id, region_name FROM " . $this->ecs->table('region') .
                    " WHERE agency_id = '$id'";
                $agency['region_list'] = $this->db->getAll($sql);
            }

            /* 取得所有管理员，标注哪些是该办事处的('this')，哪些是空闲的('free')，哪些是别的办事处的('other') */
            $sql = "SELECT user_id, user_name, CASE " .
                "WHEN agency_id = 0 THEN 'free' " .
                "WHEN agency_id = '$agency[agency_id]' THEN 'this' " .
                "ELSE 'other' END " .
                "AS type " .
                "FROM " . $this->ecs->table('admin_user');
            $agency['admin_list'] = $this->db->getAll($sql);

            $this->smarty->assign('agency', $agency);

            /* 取得地区 */
            $country_list = get_regions();
            $this->smarty->assign('countries', $country_list);

            /* 显示模板 */
            if ($is_add) {
                $this->smarty->assign('ur_here', $GLOBALS['_LANG']['add_agency']);
            } else {
                $this->smarty->assign('ur_here', $GLOBALS['_LANG']['edit_agency']);
            }
            if ($is_add) {
                $href = 'agency.php?act=list';
            } else {
                $href = 'agency.php?act=list&' . list_link_postfix();
            }
            $this->smarty->assign('action_link', array('href' => $href, 'text' => $GLOBALS['_LANG']['agency_list']));

            $this->smarty->display('agency_info.htm');
        }

        /*------------------------------------------------------ */
//-- 提交添加、编辑办事处
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
            /* 检查权限 */
            admin_priv('agency_manage');

            /* 是否添加 */
            $is_add = $_REQUEST['act'] == 'insert';

            /* 提交值 */
            $agency = array(
                'agency_id' => intval($_POST['id']),
                'agency_name' => sub_str($_POST['agency_name'], 255, false),
                'agency_desc' => $_POST['agency_desc']
            );

            /* 判断名称是否重复 */
            if (!$exc->is_only('agency_name', $agency['agency_name'], $agency['agency_id'])) {
                sys_msg($GLOBALS['_LANG']['agency_name_exist']);
            }

            /* 检查是否选择了地区 */
            if (empty($_POST['regions'])) {
                sys_msg($GLOBALS['_LANG']['no_regions']);
            }

            /* 保存办事处信息 */
            if ($is_add) {
                $this->db->autoExecute($this->ecs->table('agency'), $agency, 'INSERT');
                $agency['agency_id'] = $this->db->insert_id();
            } else {
                $this->db->autoExecute($this->ecs->table('agency'), $agency, 'UPDATE', "agency_id = '$agency[agency_id]'");
            }

            /* 更新管理员表和地区表 */
            if (!$is_add) {
                $sql = "UPDATE " . $this->ecs->table('admin_user') . " SET agency_id = 0 WHERE agency_id = '$agency[agency_id]'";
                $this->db->query($sql);

                $sql = "UPDATE " . $this->ecs->table('region') . " SET agency_id = 0 WHERE agency_id = '$agency[agency_id]'";
                $this->db->query($sql);
            }

            if (isset($_POST['admins'])) {
                $sql = "UPDATE " . $this->ecs->table('admin_user') . " SET agency_id = '$agency[agency_id]' WHERE user_id " . db_create_in($_POST['admins']);
                $this->db->query($sql);
            }

            if (isset($_POST['regions'])) {
                $sql = "UPDATE " . $this->ecs->table('region') . " SET agency_id = '$agency[agency_id]' WHERE region_id " . db_create_in($_POST['regions']);
                $this->db->query($sql);
            }

            /* 记日志 */
            if ($is_add) {
                admin_log($agency['agency_name'], 'add', 'agency');
            } else {
                admin_log($agency['agency_name'], 'edit', 'agency');
            }

            /* 清除缓存 */
            clear_cache_files();

            /* 提示信息 */
            if ($is_add) {
                $links = array(
                    array('href' => 'agency.php?act=add', 'text' => $GLOBALS['_LANG']['continue_add_agency']),
                    array('href' => 'agency.php?act=list', 'text' => $GLOBALS['_LANG']['back_agency_list'])
                );
                sys_msg($GLOBALS['_LANG']['add_agency_ok'], 0, $links);
            } else {
                $links = array(
                    array('href' => 'agency.php?act=list&' . list_link_postfix(), 'text' => $GLOBALS['_LANG']['back_agency_list'])
                );
                sys_msg($GLOBALS['_LANG']['edit_agency_ok'], 0, $links);
            }
        }
    }

    /**
     * 取得办事处列表
     * @return  array
     */
    private function get_agencylist()
    {
        $result = get_filter();
        if ($result === false) {
            /* 初始化分页参数 */
            $filter = array();
            $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'agency_id' : trim($_REQUEST['sort_by']);
            $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

            /* 查询记录总数，计算分页数 */
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('agency');
            $filter['record_count'] = $GLOBALS['db']->getOne($sql);
            $filter = page_and_size($filter);

            /* 查询记录 */
            $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('agency') . " ORDER BY $filter[sort_by] $filter[sort_order]";

            set_filter($filter, $sql);
        } else {
            $sql = $result['sql'];
            $filter = $result['filter'];
        }
        $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

        $arr = array();
        foreach ($res as $rows) {
            $arr[] = $rows;
        }

        return array('agency' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
}