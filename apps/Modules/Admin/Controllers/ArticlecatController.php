<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Exchange;

/**
 * Class ArticlecatController
 * @package App\Modules\Admin\Controllers
 */
class ArticlecatController extends Controller
{
    public function actionIndex()
    {


        /**
         *  文章分类管理程序
         */

        $exc = new Exchange($this->ecs->table("article_cat"), $this->db, 'cat_id', 'cat_name');



        /*------------------------------------------------------ */
//-- 分类列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            $articlecat = article_cat_list(0, 0, false);
            foreach ($articlecat as $key => $cat) {
                $articlecat[$key]['type_name'] = $GLOBALS['_LANG']['type_name'][$cat['cat_type']];
            }
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['02_articlecat_list']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['articlecat_add'], 'href' => 'articlecat.php?act=add'));
            $this->smarty->assign('full_page', 1);
            $this->smarty->assign('articlecat', $articlecat);


            $this->smarty->display('articlecat_list.htm');
        }

        /*------------------------------------------------------ */
//-- 查询
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'query') {
            $articlecat = article_cat_list(0, 0, false);
            foreach ($articlecat as $key => $cat) {
                $articlecat[$key]['type_name'] = $GLOBALS['_LANG']['type_name'][$cat['cat_type']];
            }
            $this->smarty->assign('articlecat', $articlecat);

            make_json_result($this->smarty->fetch('articlecat_list.htm'));
        }

        /*------------------------------------------------------ */
//-- 添加分类
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'add') {
            /* 权限判断 */
            admin_priv('article_cat');

            $this->smarty->assign('cat_select', article_cat_list(0));
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['articlecat_add']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['02_articlecat_list'], 'href' => 'articlecat.php?act=list'));
            $this->smarty->assign('form_action', 'insert');


            $this->smarty->display('articlecat_info.htm');
        }
        if ($_REQUEST['act'] == 'insert') {
            /* 权限判断 */
            admin_priv('article_cat');

            /*检查分类名是否重复*/
            $is_only = $exc->is_only('cat_name', $_POST['cat_name']);

            if (!$is_only) {
                sys_msg(sprintf($GLOBALS['_LANG']['catname_exist'], stripslashes($_POST['cat_name'])), 1);
            }

            $cat_type = 1;
            if ($_POST['parent_id'] > 0) {
                $sql = "SELECT cat_type FROM " . $this->ecs->table('article_cat') . " WHERE cat_id = '$_POST[parent_id]'";
                $p_cat_type = $this->db->getOne($sql);
                if ($p_cat_type == 2 || $p_cat_type == 3 || $p_cat_type == 5) {
                    sys_msg($GLOBALS['_LANG']['not_allow_add'], 0);
                } elseif ($p_cat_type == 4) {
                    $cat_type = 5;
                }
            }


            $sql = "INSERT INTO " . $this->ecs->table('article_cat') . "(cat_name, cat_type, cat_desc,keywords, parent_id, sort_order, show_in_nav)
           VALUES ('$_POST[cat_name]', '$cat_type',  '$_POST[cat_desc]','$_POST[keywords]', '$_POST[parent_id]', '$_POST[sort_order]', '$_POST[show_in_nav]')";
            $this->db->query($sql);

            if ($_POST['show_in_nav'] == 1) {
                $vieworder = $this->db->getOne("SELECT max(vieworder) FROM " . $this->ecs->table('nav') . " WHERE type = 'middle'");
                $vieworder += 2;
                //显示在自定义导航栏中
                $sql = "INSERT INTO " . $this->ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $_POST['cat_name'] . "', 'a', '" . $this->db->insert_id() . "','1','$vieworder','0', '" . build_uri('article_cat', array('acid' => $this->db->insert_id()), $_POST['cat_name']) . "','middle')";
                $this->db->query($sql);
            }

            admin_log($_POST['cat_name'], 'add', 'articlecat');

            $link[0]['text'] = $GLOBALS['_LANG']['continue_add'];
            $link[0]['href'] = 'articlecat.php?act=add';

            $link[1]['text'] = $GLOBALS['_LANG']['back_list'];
            $link[1]['href'] = 'articlecat.php?act=list';
            clear_cache_files();
            sys_msg($_POST['cat_name'] . $GLOBALS['_LANG']['catadd_succed'], 0, $link);
        }

        /*------------------------------------------------------ */
//-- 编辑文章分类
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit') {
            /* 权限判断 */
            admin_priv('article_cat');

            $sql = "SELECT cat_id, cat_name, cat_type, cat_desc, show_in_nav, keywords, parent_id,sort_order FROM " .
                $this->ecs->table('article_cat') . " WHERE cat_id='$_REQUEST[id]'";
            $cat = $this->db->GetRow($sql);

            if ($cat['cat_type'] == 2 || $cat['cat_type'] == 3 || $cat['cat_type'] == 4) {
                $this->smarty->assign('disabled', 1);
            }
            $options = article_cat_list(0, $cat['parent_id'], false);
            $select = '';
            $selected = $cat['parent_id'];
            foreach ($options as $var) {
                if ($var['cat_id'] == $_REQUEST['id']) {
                    continue;
                }
                $select .= '<option value="' . $var['cat_id'] . '" ';
                $select .= ' cat_type="' . $var['cat_type'] . '" ';
                $select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
                $select .= '>';
                if ($var['level'] > 0) {
                    $select .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select .= htmlspecialchars($var['cat_name']) . '</option>';
            }
            unset($options);
            $this->smarty->assign('cat', $cat);
            $this->smarty->assign('cat_select', $select);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['articlecat_edit']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['02_articlecat_list'], 'href' => 'articlecat.php?act=list'));
            $this->smarty->assign('form_action', 'update');


            $this->smarty->display('articlecat_info.htm');
        }
        if ($_REQUEST['act'] == 'update') {
            /* 权限判断 */
            admin_priv('article_cat');

            /*检查重名*/
            if ($_POST['cat_name'] != $_POST['old_catname']) {
                $is_only = $exc->is_only('cat_name', $_POST['cat_name'], $_POST['id']);

                if (!$is_only) {
                    sys_msg(sprintf($GLOBALS['_LANG']['catname_exist'], stripslashes($_POST['cat_name'])), 1);
                }
            }

            if (!isset($_POST['parent_id'])) {
                $_POST['parent_id'] = 0;
            }

            $row = $this->db->getRow("SELECT cat_type, parent_id FROM " . $this->ecs->table('article_cat') . " WHERE cat_id='$_POST[id]'");
            $cat_type = $row['cat_type'];
            if ($cat_type == 3 || $cat_type == 4) {
                $_POST['parent_id'] = $row['parent_id'];
            }

            /* 检查设定的分类的父分类是否合法 */
            $child_cat = article_cat_list($_POST['id'], 0, false);
            if (!empty($child_cat)) {
                foreach ($child_cat as $child_data) {
                    $catid_array[] = $child_data['cat_id'];
                }
            }
            if (in_array($_POST['parent_id'], $catid_array)) {
                sys_msg(sprintf($GLOBALS['_LANG']['parent_id_err'], stripslashes($_POST['cat_name'])), 1);
            }

            if ($cat_type == 1 || $cat_type == 5) {
                if ($_POST['parent_id'] > 0) {
                    $sql = "SELECT cat_type FROM " . $this->ecs->table('article_cat') . " WHERE cat_id = '$_POST[parent_id]'";
                    $p_cat_type = $this->db->getOne($sql);
                    if ($p_cat_type == 4) {
                        $cat_type = 5;
                    } else {
                        $cat_type = 1;
                    }
                } else {
                    $cat_type = 1;
                }
            }

            $dat = $this->db->getOne("SELECT cat_name, show_in_nav FROM " . $this->ecs->table('article_cat') . " WHERE cat_id = '" . $_POST['id'] . "'");
            if ($exc->edit("cat_name = '$_POST[cat_name]', cat_desc ='$_POST[cat_desc]', keywords='$_POST[keywords]',parent_id = '$_POST[parent_id]', cat_type='$cat_type', sort_order='$_POST[sort_order]', show_in_nav = '$_POST[show_in_nav]'", $_POST['id'])) {
                if ($_POST['cat_name'] != $dat['cat_name']) {
                    //如果分类名称发生了改变
                    $sql = "UPDATE " . $this->ecs->table('nav') . " SET name = '" . $_POST['cat_name'] . "' WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'";
                    $this->db->query($sql);
                }
                if ($_POST['show_in_nav'] != $dat['show_in_nav']) {
                    if ($_POST['show_in_nav'] == 1) {
                        //显示
                        $nid = $this->db->getOne("SELECT id FROM " . $this->ecs->table('nav') . " WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'");
                        if (empty($nid)) {
                            $vieworder = $this->db->getOne("SELECT max(vieworder) FROM " . $this->ecs->table('nav') . " WHERE type = 'middle'");
                            $vieworder += 2;
                            $uri = build_uri('article_cat', array('acid' => $_POST['id']), $_POST['cat_name']);
                            //不存在
                            $sql = "INSERT INTO " . $this->ecs->table('nav') .
                                " (name,ctype,cid,ifshow,vieworder,opennew,url,type) " .
                                "VALUES('" . $_POST['cat_name'] . "', 'a', '" . $_POST['id'] . "','1','$vieworder','0', '" . $uri . "','middle')";
                        } else {
                            $sql = "UPDATE " . $this->ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'";
                        }
                        $this->db->query($sql);
                    } else {
                        //去除
                        $this->db->query("UPDATE " . $this->ecs->table('nav') . " SET ifshow = 0 WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'");
                    }
                }
                $link[0]['text'] = $GLOBALS['_LANG']['back_list'];
                $link[0]['href'] = 'articlecat.php?act=list';
                $note = sprintf($GLOBALS['_LANG']['catedit_succed'], $_POST['cat_name']);
                admin_log($_POST['cat_name'], 'edit', 'articlecat');
                clear_cache_files();
                sys_msg($note, 0, $link);
            } else {
                die($this->db->error());
            }
        }


        /*------------------------------------------------------ */
//-- 编辑文章分类的排序
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit_sort_order') {
            check_authz_json('article_cat');

            $id = intval($_POST['id']);
            $order = json_str_iconv(trim($_POST['val']));

            /* 检查输入的值是否合法 */
            if (!preg_match("/^[0-9]+$/", $order)) {
                make_json_error(sprintf($GLOBALS['_LANG']['enter_int'], $order));
            } else {
                if ($exc->edit("sort_order = '$order'", $id)) {
                    clear_cache_files();
                    make_json_result(stripslashes($order));
                } else {
                    make_json_error($this->db->error());
                }
            }
        }

        /*------------------------------------------------------ */
//-- 删除文章分类
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'remove') {
            check_authz_json('article_cat');

            $id = intval($_GET['id']);

            $sql = "SELECT cat_type FROM " . $this->ecs->table('article_cat') . " WHERE cat_id = '$id'";
            $cat_type = $this->db->getOne($sql);
            if ($cat_type == 2 || $cat_type == 3 || $cat_type == 4) {
                /* 系统保留分类，不能删除 */
                make_json_error($GLOBALS['_LANG']['not_allow_remove']);
            }

            $sql = "SELECT COUNT(*) FROM " . $this->ecs->table('article_cat') . " WHERE parent_id = '$id'";
            if ($this->db->getOne($sql) > 0) {
                /* 还有子分类，不能删除 */
                make_json_error($GLOBALS['_LANG']['is_fullcat']);
            }

            /* 非空的分类不允许删除 */
            $sql = "SELECT COUNT(*) FROM " . $this->ecs->table('article') . " WHERE cat_id = '$id'";
            if ($this->db->getOne($sql) > 0) {
                make_json_error(sprintf($GLOBALS['_LANG']['not_emptycat']));
            } else {
                $exc->drop($id);
                $this->db->query("DELETE FROM " . $this->ecs->table('nav') . "WHERE  ctype = 'a' AND cid = '$id' AND type = 'middle'");
                clear_cache_files();
                admin_log($cat_name, 'remove', 'category');
            }

            $url = 'articlecat.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

            ecs_header("Location: $url\n");
            exit;
        }
        /*------------------------------------------------------ */
//-- 切换是否显示在导航栏
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'toggle_show_in_nav') {
            check_authz_json('cat_manage');

            $id = intval($_POST['id']);
            $val = intval($_POST['val']);

            if ($this->cat_update($id, array('show_in_nav' => $val)) != false) {
                if ($val == 1) {
                    //显示
                    $nid = $this->db->getOne("SELECT id FROM " . $this->ecs->table('nav') . " WHERE ctype='a' AND cid='$id' AND type = 'middle'");
                    if (empty($nid)) {
                        //不存在
                        $vieworder = $this->db->getOne("SELECT max(vieworder) FROM " . $this->ecs->table('nav') . " WHERE type = 'middle'");
                        $vieworder += 2;
                        $catname = $this->db->getOne("SELECT cat_name FROM " . $this->ecs->table('article_cat') . " WHERE cat_id = '$id'");
                        $uri = build_uri('article_cat', array('acid' => $id), $_POST['cat_name']);

                        $sql = "INSERT INTO " . $this->ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) " .
                            "VALUES('" . $catname . "', 'a', '$id','1','$vieworder','0', '" . $uri . "','middle')";
                    } else {
                        $sql = "UPDATE " . $this->ecs->table('nav') . " SET ifshow = 1 WHERE ctype='a' AND cid='$id' AND type = 'middle'";
                    }
                    $this->db->query($sql);
                } else {
                    //去除
                    $this->db->query("UPDATE " . $this->ecs->table('nav') . " SET ifshow = 0 WHERE ctype='a' AND cid='$id' AND type = 'middle'");
                }
                clear_cache_files();
                make_json_result($val);
            } else {
                make_json_error($this->db->error());
            }
        }
    }

    /**
     * 添加商品分类
     *
     * @param   integer $cat_id
     * @param   array $args
     *
     * @return  mix
     */
    private function cat_update($cat_id, $args)
    {
        if (empty($args) || empty($cat_id)) {
            return false;
        }

        return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('article_cat'), $args, 'update', "cat_id='$cat_id'");
    }
}