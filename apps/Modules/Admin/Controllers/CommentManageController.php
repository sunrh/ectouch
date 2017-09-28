<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class CommentManageController
 * @package App\Modules\Admin\Controllers
 */
class CommentManageController extends Controller
{
    public function actionIndex()
    {


        /**
         *  用户评论管理程序
         */




        /*------------------------------------------------------ */
//-- 获取没有回复的评论列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            /* 检查权限 */
            admin_priv('comment_priv');

            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['05_comment_manage']);
            $this->smarty->assign('full_page', 1);

            $list = $this->get_comment_list();

            $this->smarty->assign('comment_list', $list['item']);
            $this->smarty->assign('filter', $list['filter']);
            $this->smarty->assign('record_count', $list['record_count']);
            $this->smarty->assign('page_count', $list['page_count']);

            $sort_flag = sort_flag($list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);


            $this->smarty->display('comment_list.htm');
        }

        /*------------------------------------------------------ */
//-- 翻页、搜索、排序
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'query') {
            $list = $this->get_comment_list();

            $this->smarty->assign('comment_list', $list['item']);
            $this->smarty->assign('filter', $list['filter']);
            $this->smarty->assign('record_count', $list['record_count']);
            $this->smarty->assign('page_count', $list['page_count']);

            $sort_flag = sort_flag($list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('comment_list.htm'), '',
                array('filter' => $list['filter'], 'page_count' => $list['page_count']));
        }

        /*------------------------------------------------------ */
//-- 回复用户评论(同时查看评论详情)
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'reply') {
            /* 检查权限 */
            admin_priv('comment_priv');

            $comment_info = array();
            $reply_info = array();
            $id_value = array();

            /* 获取评论详细信息并进行字符处理 */
            $sql = "SELECT * FROM " . $this->ecs->table('comment') . " WHERE comment_id = '$_REQUEST[id]'";
            $comment_info = $this->db->getRow($sql);
            $comment_info['content'] = str_replace('\r\n', '<br />', htmlspecialchars($comment_info['content']));
            $comment_info['content'] = nl2br(str_replace('\n', '<br />', $comment_info['content']));
            $comment_info['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $comment_info['add_time']);

            /* 获得评论回复内容 */
            $sql = "SELECT * FROM " . $this->ecs->table('comment') . " WHERE parent_id = '$_REQUEST[id]'";
            $reply_info = $this->db->getRow($sql);

            if (empty($reply_info)) {
                $reply_info['content'] = '';
                $reply_info['add_time'] = '';
            } else {
                $reply_info['content'] = nl2br(htmlspecialchars($reply_info['content']));
                $reply_info['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $reply_info['add_time']);
            }
            /* 获取管理员的用户名和Email地址 */
            $sql = "SELECT user_name, email FROM " . $this->ecs->table('admin_user') .
                " WHERE user_id = '" . session('admin_id') . "'";
            $admin_info = $this->db->getRow($sql);

            /* 取得评论的对象(文章或者商品) */
            if ($comment_info['comment_type'] == 0) {
                $sql = "SELECT goods_name FROM " . $this->ecs->table('goods') .
                    " WHERE goods_id = '$comment_info[id_value]'";
                $id_value = $this->db->getOne($sql);
            } else {
                $sql = "SELECT title FROM " . $this->ecs->table('article') .
                    " WHERE article_id='$comment_info[id_value]'";
                $id_value = $this->db->getOne($sql);
            }

            /* 模板赋值 */
            $this->smarty->assign('msg', $comment_info); //评论信息
            $this->smarty->assign('admin_info', $admin_info);   //管理员信息
            $this->smarty->assign('reply_info', $reply_info);   //回复的内容
            $this->smarty->assign('id_value', $id_value);  //评论的对象
            $this->smarty->assign('send_fail', !empty($_REQUEST['send_ok']));

            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['comment_info']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['05_comment_manage'],
                'href' => 'comment_manage.php?act=list'));

            /* 页面显示 */

            $this->smarty->display('comment_info.htm');
        }
        /*------------------------------------------------------ */
//-- 处理 回复用户评论
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'action') {
            admin_priv('comment_priv');

            /* 获取IP地址 */
            $ip = real_ip();

            /* 获得评论是否有回复 */
            $sql = "SELECT comment_id, content, parent_id FROM " . $this->ecs->table('comment') .
                " WHERE parent_id = '$_REQUEST[comment_id]'";
            $reply_info = $this->db->getRow($sql);

            if (!empty($reply_info['content'])) {
                /* 更新回复的内容 */
                $sql = "UPDATE " . $this->ecs->table('comment') . " SET " .
                    "email     = '$_POST[email]', " .
                    "user_name = '$_POST[user_name]', " .
                    "content   = '$_POST[content]', " .
                    "add_time  =  '" . gmtime() . "', " .
                    "ip_address= '$ip', " .
                    "status    = 0" .
                    " WHERE comment_id = '" . $reply_info['comment_id'] . "'";
            } else {
                /* 插入回复的评论内容 */
                $sql = "INSERT INTO " . $this->ecs->table('comment') . " (comment_type, id_value, email, user_name , " .
                    "content, add_time, ip_address, status, parent_id) " .
                    "VALUES('$_POST[comment_type]', '$_POST[id_value]','$_POST[email]', " .
                    "'" . session('admin_name') . "','$_POST[content]','" . gmtime() . "', '$ip', '0', '$_POST[comment_id]')";
            }
            $this->db->query($sql);

            /* 更新当前的评论状态为已回复并且可以显示此条评论 */
            $sql = "UPDATE " . $this->ecs->table('comment') . " SET status = 1 WHERE comment_id = '$_POST[comment_id]'";
            $this->db->query($sql);

            /* 邮件通知处理流程 */
            if (!empty($_POST['send_email_notice']) or isset($_POST['remail'])) {
                //获取邮件中的必要内容
                $sql = 'SELECT user_name, email, content ' .
                    'FROM ' . $this->ecs->table('comment') .
                    " WHERE comment_id ='$_REQUEST[comment_id]'";
                $comment_info = $this->db->getRow($sql);

                /* 设置留言回复模板所需要的内容信息 */
                $template = get_mail_template('recomment');

                $this->smarty->assign('user_name', $comment_info['user_name']);
                $this->smarty->assign('recomment', $_POST['content']);
                $this->smarty->assign('comment', $comment_info['content']);
                $this->smarty->assign('shop_name', "<a href='" . $this->ecs->url() . "'>" . $GLOBALS['_CFG']['shop_name'] . '</a>');
                $this->smarty->assign('send_date', date('Y-m-d'));

                $content = $this->smarty->fetch('str:' . $template['template_content']);

                /* 发送邮件 */
                if (send_mail($comment_info['user_name'], $comment_info['email'], $template['template_subject'], $content, $template['is_html'])) {
                    $send_ok = 0;
                } else {
                    $send_ok = 1;
                }
            }

            /* 清除缓存 */
            clear_cache_files();

            /* 记录管理员操作 */
            admin_log(addslashes($GLOBALS['_LANG']['reply']), 'edit', 'users_comment');

            ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[comment_id]&send_ok=$send_ok\n");
            exit;
        }
        /*------------------------------------------------------ */
//-- 更新评论的状态为显示或者禁止
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'check') {
            if ($_REQUEST['check'] == 'allow') {
                /* 允许评论显示 */
                $sql = "UPDATE " . $this->ecs->table('comment') . " SET status = 1 WHERE comment_id = '$_REQUEST[id]'";
                $this->db->query($sql);

                //add_feed($_REQUEST['id'], COMMENT_GOODS);

                /* 清除缓存 */
                clear_cache_files();

                ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");
                exit;
            } else {
                /* 禁止评论显示 */
                $sql = "UPDATE " . $this->ecs->table('comment') . " SET status = 0 WHERE comment_id = '$_REQUEST[id]'";
                $this->db->query($sql);

                /* 清除缓存 */
                clear_cache_files();

                ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");
                exit;
            }
        }

        /*------------------------------------------------------ */
//-- 删除某一条评论
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('comment_priv');

            $id = intval($_GET['id']);

            $sql = "DELETE FROM " . $this->ecs->table('comment') . " WHERE comment_id = '$id'";
            $res = $this->db->query($sql);
            if ($res) {
                $this->db->query("DELETE FROM " . $this->ecs->table('comment') . " WHERE parent_id = '$id'");
            }

            admin_log('', 'remove', 'ads');

            $url = 'comment_manage.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

            ecs_header("Location: $url\n");
            exit;
        }

        /*------------------------------------------------------ */
//-- 批量删除用户评论
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'batch') {
            admin_priv('comment_priv');
            $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'deny';

            if (isset($_POST['checkboxes'])) {
                switch ($action) {
                    case 'remove':
                        $this->db->query("DELETE FROM " . $this->ecs->table('comment') . " WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));
                        $this->db->query("DELETE FROM " . $this->ecs->table('comment') . " WHERE " . db_create_in($_POST['checkboxes'], 'parent_id'));
                        break;

                    case 'allow':
                        $this->db->query("UPDATE " . $this->ecs->table('comment') . " SET status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));
                        break;

                    case 'deny':
                        $this->db->query("UPDATE " . $this->ecs->table('comment') . " SET status = 0  WHERE " . db_create_in($_POST['checkboxes'], 'comment_id'));
                        break;

                    default:
                        break;
                }

                clear_cache_files();
                $action = ($action == 'remove') ? 'remove' : 'edit';
                admin_log('', $action, 'adminlog');

                $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'comment_manage.php?act=list');
                sys_msg(sprintf($GLOBALS['_LANG']['batch_drop_success'], count($_POST['checkboxes'])), 0, $link);
            } else {
                /* 提示信息 */
                $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'comment_manage.php?act=list');
                sys_msg($GLOBALS['_LANG']['no_select_comment'], 0, $link);
            }
        }
    }

    /**
     * 获取评论列表
     * @access  public
     * @return  array
     */
    private function get_comment_list()
    {
        /* 查询条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = (!empty($filter['keywords'])) ? " AND content LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';

        $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('comment') . " WHERE parent_id = 0 $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 获取评论数据 */
        $arr = array();
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('comment') . " WHERE parent_id = 0 $where " .
            " ORDER BY $filter[sort_by] $filter[sort_order] " .
            " LIMIT " . $filter['start'] . ", $filter[page_size]";
        $res = $GLOBALS['db']->query($sql);

        foreach ($res as $row) {
            $sql = ($row['comment_type'] == 0) ?
                "SELECT goods_name FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id='$row[id_value]'" :
                "SELECT title FROM " . $GLOBALS['ecs']->table('article') . " WHERE article_id='$row[id_value]'";
            $row['title'] = $GLOBALS['db']->getOne($sql);

            /* 标记是否回复过 */
//        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE parent_id = '$row[comment_id]'";
//        $row['is_reply'] =  ($GLOBALS['db']->getOne($sql) > 0) ?
//            $GLOBALS['_LANG']['yes_reply'] : $GLOBALS['_LANG']['no_reply'];

            $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);

            $arr[] = $row;
        }
        $filter['keywords'] = stripslashes($filter['keywords']);
        $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }
}