<?php

namespace App\Modules\Admin\Controllers;

/**
 * Class TagManageController
 * @package App\Modules\Admin\Controllers
 */
class TagManageController extends Controller
{
    public function actionIndex()
    {

        /*------------------------------------------------------ */
//-- 鑾峰彇鏍囩?鏁版嵁鍒楄〃
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            /* 鏉冮檺鍒ゆ柇 */
            admin_priv('tag_manage');

            /* 妯℃澘璧嬪€ */
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['tag_list']);
            $this->smarty->assign('action_link', array('href' => 'tag_manage.php?act=add', 'text' => $GLOBALS['_LANG']['add_tag']));
            $this->smarty->assign('full_page', 1);

            $tag_list = $this->get_tag_list();
            $this->smarty->assign('tag_list', $tag_list['tags']);
            $this->smarty->assign('filter', $tag_list['filter']);
            $this->smarty->assign('record_count', $tag_list['record_count']);
            $this->smarty->assign('page_count', $tag_list['page_count']);

            $sort_flag = sort_flag($tag_list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            /* 椤甸潰鏄剧ず */

            $this->smarty->display('tag_manage.htm');
        }

        /*------------------------------------------------------ */
//-- 娣诲姞 ,缂栬緫
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
            admin_priv('tag_manage');

            $is_add = $_REQUEST['act'] == 'add';
            $this->smarty->assign('insert_or_update', $is_add ? 'insert' : 'update');

            if ($is_add) {
                $tag = array(
                    'tag_id' => 0,
                    'tag_words' => '',
                    'goods_id' => 0,
                    'goods_name' => $GLOBALS['_LANG']['pls_select_goods']
                );
                $this->smarty->assign('ur_here', $GLOBALS['_LANG']['add_tag']);
            } else {
                $tag_id = $_GET['id'];
                $tag = $this->get_tag_info($tag_id);
                $tag['tag_words'] = htmlspecialchars($tag['tag_words']);
                $this->smarty->assign('ur_here', $GLOBALS['_LANG']['tag_edit']);
            }
            $this->smarty->assign('tag', $tag);
            $this->smarty->assign('action_link', array('href' => 'tag_manage.php?act=list', 'text' => $GLOBALS['_LANG']['tag_list']));


            $this->smarty->display('tag_edit.htm');
        }

        /*------------------------------------------------------ */
//-- 鏇存柊
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
            admin_priv('tag_manage');

            $is_insert = $_REQUEST['act'] == 'insert';

            $tag_words = empty($_POST['tag_name']) ? '' : trim($_POST['tag_name']);
            $id = intval($_POST['id']);
            $goods_id = intval($_POST['goods_id']);
            if ($goods_id <= 0) {
                sys_msg($GLOBALS['_LANG']['pls_select_goods']);
            }

            if (!$this->tag_is_only($tag_words, $id, $goods_id)) {
                sys_msg(sprintf($GLOBALS['_LANG']['tagword_exist'], $tag_words));
            }

            if ($is_insert) {
                $sql = 'INSERT INTO ' . $this->ecs->table('tag') . '(tag_id, goods_id, tag_words)' .
                    " VALUES('$id', '$goods_id', '$tag_words')";
                $this->db->query($sql);

                admin_log($tag_words, 'add', 'tag');

                /* 娓呴櫎缂撳瓨 */
                clear_cache_files();

                $link[0]['text'] = $GLOBALS['_LANG']['back_list'];
                $link[0]['href'] = 'tag_manage.php?act=list';

                sys_msg($GLOBALS['_LANG']['tag_add_success'], 0, $link);
            } else {
                $this->edit_tag($tag_words, $id, $goods_id);

                /* 娓呴櫎缂撳瓨 */
                clear_cache_files();

                $link[0]['text'] = $GLOBALS['_LANG']['back_list'];
                $link[0]['href'] = 'tag_manage.php?act=list';

                sys_msg($GLOBALS['_LANG']['tag_edit_success'], 0, $link);
            }
        }

        /*------------------------------------------------------ */
//-- 缈婚〉锛屾帓搴
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'query') {
            check_authz_json('tag_manage');

            $tag_list = $this->get_tag_list();
            $this->smarty->assign('tag_list', $tag_list['tags']);
            $this->smarty->assign('filter', $tag_list['filter']);
            $this->smarty->assign('record_count', $tag_list['record_count']);
            $this->smarty->assign('page_count', $tag_list['page_count']);

            $sort_flag = sort_flag($tag_list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('tag_manage.htm'), '',
                array('filter' => $tag_list['filter'], 'page_count' => $tag_list['page_count']));
        }

        /*------------------------------------------------------ */
//-- 鎼滅储
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'search_goods') {
            check_authz_json('tag_manage');

            // include_once(ROOT_PATH . 'includes/cls_json.php');

            $json = new Json();
            $filter = $json->decode($_GET['JSON']);
            $arr = get_goods_list($filter);
            if (empty($arr)) {
                $arr[0] = array(
                    'goods_id' => 0,
                    'goods_name' => ''
                );
            }

            make_json_result($arr);
        }

        /*------------------------------------------------------ */
//-- 鎵归噺鍒犻櫎鏍囩?
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'batch_drop') {
            admin_priv('tag_manage');

            if (isset($_POST['checkboxes'])) {
                $count = 0;
                foreach ($_POST['checkboxes'] as $key => $id) {
                    $sql = "DELETE FROM " . $this->ecs->table('tag') . " WHERE tag_id='$id'";
                    $this->db->query($sql);

                    $count++;
                }

                admin_log($count, 'remove', 'tag_manage');
                clear_cache_files();

                $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'tag_manage.php?act=list');
                sys_msg(sprintf($GLOBALS['_LANG']['drop_success'], $count), 0, $link);
            } else {
                $link[] = array('text' => $GLOBALS['_LANG']['back_list'], 'href' => 'tag_manage.php?act=list');
                sys_msg($GLOBALS['_LANG']['no_select_tag'], 0, $link);
            }
        }

        /*------------------------------------------------------ */
//-- 鍒犻櫎鏍囩?
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('tag_manage');

            // include_once(ROOT_PATH . 'includes/cls_json.php');
            $json = new Json();

            $id = intval($_GET['id']);

            /* 鑾峰彇鍒犻櫎鐨勬爣绛剧殑鍚嶇О */
            $tag_name = $this->db->getOne("SELECT tag_words FROM " . $this->ecs->table('tag') . " WHERE tag_id = '$id'");

            $sql = "DELETE FROM " . $this->ecs->table('tag') . " WHERE tag_id = '$id'";
            $result = $GLOBALS['db']->query($sql);
            if ($result) {
                /* 绠＄悊鍛樻棩蹇 */
                admin_log(addslashes($tag_name), 'remove', 'tag_manage');

                $url = 'tag_manage.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
                ecs_header("Location: $url\n");
                exit;
            } else {
                make_json_error($this->db->error());
            }
        }

        /*------------------------------------------------------ */
//-- 缂栬緫鏍囩?鍚嶇О
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == "edit_tag_name") {
            check_authz_json('tag_manage');

            $name = json_str_iconv(trim($_POST['val']));
            $id = intval($_POST['id']);

            if (!$this->tag_is_only($name, $id)) {
                make_json_error(sprintf($GLOBALS['_LANG']['tagword_exist'], $name));
            } else {
                $this->edit_tag($name, $id);
                make_json_result(stripslashes($name));
            }
        }
    }

    /**
     * 鍒ゆ柇鍚屼竴鍟嗗搧鐨勬爣绛炬槸鍚﹀敮涓€
     *
     * @param $name  鏍囩?鍚
     * @param $id  鏍囩?id
     * @return bool
     */
    private function tag_is_only($name, $tag_id, $goods_id = '')
    {
        if (empty($goods_id)) {
            $db = $GLOBALS['db'];
            $sql = 'SELECT goods_id FROM ' . $GLOBALS['ecs']->table('tag') . " WHERE tag_id = '$tag_id'";
            $row = $GLOBALS['db']->getRow($sql);
            $goods_id = $row['goods_id'];
        }

        $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('tag') . " WHERE tag_words = '$name'" .
            " AND goods_id = '$goods_id' AND tag_id != '$tag_id'";

        if ($GLOBALS['db']->getOne($sql) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 鏇存柊鏍囩?
     *
     * @param  $name
     * @param  $id
     * @return void
     */
    private function edit_tag($name, $id, $goods_id = '')
    {
        $db = $GLOBALS['db'];
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('tag') . " SET tag_words = '$name'";
        if (!empty($goods_id)) {
            $sql .= ", goods_id = '$goods_id'";
        }
        $sql .= " WHERE tag_id = '$id'";
        $GLOBALS['db']->query($sql);

        admin_log($name, 'edit', 'tag');
    }

    /**
     * 鑾峰彇鏍囩?鏁版嵁鍒楄〃
     * @access  public
     * @return  array
     */
    private function get_tag_list()
    {
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 't.tag_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('tag');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        $sql = "SELECT t.tag_id, u.user_name, t.goods_id, g.goods_name, t.tag_words " .
            "FROM " . $GLOBALS['ecs']->table('tag') . " AS t " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('users') . " AS u ON u.user_id=t.user_id " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.goods_id=t.goods_id " .
            "ORDER by $filter[sort_by] $filter[sort_order] LIMIT " . $filter['start'] . ", " . $filter['page_size'];
        $row = $GLOBALS['db']->getAll($sql);
        foreach ($row as $k => $v) {
            $row[$k]['tag_words'] = htmlspecialchars($v['tag_words']);
        }

        $arr = array('tags' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }

    /**
     * 鍙栧緱鏍囩?鐨勪俊鎭
     * return array
     */
    private function get_tag_info($tag_id)
    {
        $sql = 'SELECT t.tag_id, t.tag_words, t.goods_id, g.goods_name FROM ' . $GLOBALS['ecs']->table('tag') . ' AS t' .
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON t.goods_id=g.goods_id' .
            " WHERE tag_id = '$tag_id'";
        $row = $GLOBALS['db']->getRow($sql);

        return $row;
    }
}