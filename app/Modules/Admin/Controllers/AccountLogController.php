<?php

namespace App\Modules\Admin\Controllers;

/**
 * 管理中心帐户变动记录
 * Class AccountLogController
 * @package App\Modules\Admin\Controllers
 */
class AccountLogController extends Controller
{
    public function actionIndex()
    {
        load_helper('order');

        /**
         * 办事处列表
         */
        if ($_REQUEST['act'] == 'list') {
            /* 检查参数 */
            $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
            if ($user_id <= 0) {
                sys_msg('invalid param');
            }
            $user = user_info($user_id);
            if (empty($user)) {
                sys_msg($GLOBALS['_LANG']['user_not_exist']);
            }
            $this->smarty->assign('user', $user);

            if (empty($_REQUEST['account_type']) || !in_array($_REQUEST['account_type'],
                    array('user_money', 'frozen_money', 'rank_points', 'pay_points'))) {
                $account_type = '';
            } else {
                $account_type = $_REQUEST['account_type'];
            }
            $this->smarty->assign('account_type', $account_type);

            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['account_list']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['add_account'], 'href' => 'account_log.php?act=add&user_id=' . $user_id));
            $this->smarty->assign('full_page', 1);

            $account_list = $this->get_accountlist($user_id, $account_type);
            $this->smarty->assign('account_list', $account_list['account']);
            $this->smarty->assign('filter', $account_list['filter']);
            $this->smarty->assign('record_count', $account_list['record_count']);
            $this->smarty->assign('page_count', $account_list['page_count']);


            $this->smarty->display('account_list.htm');
        }

        /**
         * 排序、分页、查询
         */
        if ($_REQUEST['act'] == 'query') {
            /* 检查参数 */
            $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
            if ($user_id <= 0) {
                sys_msg('invalid param');
            }

            $user = user_info($user_id);
            if (empty($user)) {
                sys_msg($GLOBALS['_LANG']['user_not_exist']);
            }
            $this->smarty->assign('user', $user);

            if (empty($_REQUEST['account_type']) || !in_array($_REQUEST['account_type'],
                    array('user_money', 'frozen_money', 'rank_points', 'pay_points'))) {
                $account_type = '';
            } else {
                $account_type = $_REQUEST['account_type'];
            }
            $this->smarty->assign('account_type', $account_type);

            $account_list = $this->get_accountlist($user_id, $account_type);
            $this->smarty->assign('account_list', $account_list['account']);
            $this->smarty->assign('filter', $account_list['filter']);
            $this->smarty->assign('record_count', $account_list['record_count']);
            $this->smarty->assign('page_count', $account_list['page_count']);

            make_json_result($this->smarty->fetch('account_list.htm'), '',
                array('filter' => $account_list['filter'], 'page_count' => $account_list['page_count']));
        }

        /**
         * 调节帐户
         */
        if ($_REQUEST['act'] == 'add') {
            /* 检查权限 */
            admin_priv('account_manage');
            /* 检查参数 */
            $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
            if ($user_id <= 0) {
                sys_msg('invalid param');
            }
            $user = user_info($user_id);
            if (empty($user)) {
                sys_msg($GLOBALS['_LANG']['user_not_exist']);
            }
            $this->smarty->assign('user', $user);

            /* 显示模板 */
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['add_account']);
            $this->smarty->assign('action_link', array('href' => 'account_log.php?act=list&user_id=' . $user_id, 'text' => $GLOBALS['_LANG']['account_list']));

            $this->smarty->display('account_info.htm');
        }

        /**
         * 提交添加、编辑办事处
         */
        if ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
            /* 检查权限 */
            admin_priv('account_manage');
            $token = trim($_POST['token']);
            if ($token != $GLOBALS['_CFG']['token']) {
                sys_msg($GLOBALS['_LANG']['no_account_change'], 1);
            }

            /* 检查参数 */
            $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
            if ($user_id <= 0) {
                sys_msg('invalid param');
            }
            $user = user_info($user_id);
            if (empty($user)) {
                sys_msg($GLOBALS['_LANG']['user_not_exist']);
            }

            /* 提交值 */
            $change_desc = sub_str($_POST['change_desc'], 255, false);
            $user_money = floatval($_POST['add_sub_user_money']) * abs(floatval($_POST['user_money']));
            $frozen_money = floatval($_POST['add_sub_frozen_money']) * abs(floatval($_POST['frozen_money']));
            $rank_points = floatval($_POST['add_sub_rank_points']) * abs(floatval($_POST['rank_points']));
            $pay_points = floatval($_POST['add_sub_pay_points']) * abs(floatval($_POST['pay_points']));

            if ($user_money == 0 && $frozen_money == 0 && $rank_points == 0 && $pay_points == 0) {
                sys_msg($GLOBALS['_LANG']['no_account_change']);
            }

            /* 保存 */
            log_account_change($user_id, $user_money, $frozen_money, $rank_points, $pay_points, $change_desc, ACT_ADJUSTING);

            /* 提示信息 */
            $links = array(
                array('href' => 'account_log.php?act=list&user_id=' . $user_id, 'text' => $GLOBALS['_LANG']['account_list'])
            );
            sys_msg($GLOBALS['_LANG']['log_account_change_ok'], 0, $links);
        }
    }

    /**
     * 取得帐户明细
     * @param   int $user_id 用户id
     * @param   string $account_type 帐户类型：空表示所有帐户，user_money表示可用资金，
     *                  frozen_money表示冻结资金，rank_points表示等级积分，pay_points表示消费积分
     * @return  array
     */
    private function get_accountlist($user_id, $account_type = '')
    {
        /* 检查参数 */
        $where = " WHERE user_id = '$user_id' ";
        if (in_array($account_type, array('user_money', 'frozen_money', 'rank_points', 'pay_points'))) {
            $where .= " AND $account_type <> 0 ";
        }

        /* 初始化分页参数 */
        $filter = array(
            'user_id' => $user_id,
            'account_type' => $account_type
        );

        /* 查询记录总数，计算分页数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('account_log') . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('account_log') . $where .
            " ORDER BY log_id DESC";
        $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

        $arr = array();
        foreach ($res as $row) {
            $row['change_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['change_time']);
            $arr[] = $row;
        }

        return array('account' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }
}
