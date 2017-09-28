<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Exchange;
use App\Libraries\Image;

/**
 * Class CardController
 * @package App\Modules\Admin\Controllers
 */
class CardController extends Controller
{
    public function actionIndex()
    {


        /**
         *  贺卡管理程序
         */

        $image = new Image($GLOBALS['_CFG']['bgcolor']);

        $exc = new Exchange($this->ecs->table("card"), $this->db, 'card_id', 'card_name');

        /*------------------------------------------------------ */
//-- 包装列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'list') {
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['07_card_list']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['card_add'], 'href' => 'card.php?act=add'));
            $this->smarty->assign('full_page', 1);

            $cards_list = $this->cards_list();

            $this->smarty->assign('card_list', $cards_list['card_list']);
            $this->smarty->assign('filter', $cards_list['filter']);
            $this->smarty->assign('record_count', $cards_list['record_count']);
            $this->smarty->assign('page_count', $cards_list['page_count']);

            $this->smarty->display('card_list.htm');
        }

        /*------------------------------------------------------ */
//-- ajax列表
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'query') {
            $cards_list = $this->cards_list();
            $this->smarty->assign('card_list', $cards_list['card_list']);
            $this->smarty->assign('filter', $cards_list['filter']);
            $this->smarty->assign('record_count', $cards_list['record_count']);
            $this->smarty->assign('page_count', $cards_list['page_count']);

            $sort_flag = sort_flag($cards_list['filter']);
            $this->smarty->assign($sort_flag['tag'], $sort_flag['img']);

            make_json_result($this->smarty->fetch('card_list.htm'), '', array('filter' => $cards_list['filter'], 'page_count' => $cards_list['page_count']));
        }
        /*------------------------------------------------------ */
//-- 删除贺卡
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'remove') {
            /* 检查权限 */
            check_authz_json('card_manage');

            $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

            $name = $exc->get_name($card_id);
            $img = $exc->get_name($card_id, 'card_img');

            if ($exc->drop($card_id)) {
                /* 删除图片 */
                if (!empty($img)) {
                    @unlink('../' . DATA_DIR . '/cardimg/' . $img);
                }
                admin_log(addslashes($name), 'remove', 'card');

                $url = 'card.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

                ecs_header("Location: $url\n");
                exit;
            } else {
                make_json_error($this->db->error());
            }
        }
        /*------------------------------------------------------ */
//-- 添加新包装
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'add') {
            /* 权限判断 */
            admin_priv('card_manage');

            /*初始化显示*/
            $card['card_fee'] = 0;
            $card['free_money'] = 0;

            $this->smarty->assign('card', $card);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['card_add']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['07_card_list'], 'href' => 'card.php?act=list'));
            $this->smarty->assign('form_action', 'insert');


            $this->smarty->display('card_info.htm');
        }
        if ($_REQUEST['act'] == 'insert') {
            /* 权限判断 */
            admin_priv('card_manage');

            /*检查包装名是否重复*/
            $is_only = $exc->is_only('card_name', $_POST['card_name']);

            if (!$is_only) {
                sys_msg(sprintf($GLOBALS['_LANG']['cardname_exist'], stripslashes($_POST['card_name'])), 1);
            }

            /*处理图片*/
            $img_name = basename($image->upload_image($_FILES['card_img'], "cardimg"));

            /*插入数据*/
            $sql = "INSERT INTO " . $this->ecs->table('card') . "(card_name, card_fee, free_money, card_desc, card_img)
            VALUES ('$_POST[card_name]', '$_POST[card_fee]', '$_POST[free_money]', '$_POST[card_desc]', '$img_name')";
            $this->db->query($sql);

            admin_log($_POST['card_name'], 'add', 'card');

            /*添加链接*/
            $link[0]['text'] = $GLOBALS['_LANG']['continue_add'];
            $link[0]['href'] = 'card.php?act=add';

            $link[1]['text'] = $GLOBALS['_LANG']['back_list'];
            $link[1]['href'] = 'card.php?act=list';

            sys_msg($_POST['card_name'] . $GLOBALS['_LANG']['cardadd_succeed'], 0, $link);
        }

        /*------------------------------------------------------ */
//-- 编辑包装
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit') {
            /* 权限判断 */
            admin_priv('card_manage');

            $sql = "SELECT card_id, card_name, card_fee, free_money, card_desc, card_img FROM " . $this->ecs->table('card') . " WHERE card_id='$_REQUEST[id]'";
            $card = $this->db->GetRow($sql);

            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['card_edit']);
            $this->smarty->assign('action_link', array('text' => $GLOBALS['_LANG']['07_card_list'], 'href' => 'card.php?act=list&' . list_link_postfix()));
            $this->smarty->assign('card', $card);
            $this->smarty->assign('form_action', 'update');


            $this->smarty->display('card_info.htm');
        }
        if ($_REQUEST['act'] == 'update') {
            /* 权限判断 */
            admin_priv('card_manage');

            if ($_POST['card_name'] != $_POST['old_cardname']) {
                /*检查品牌名是否相同*/
                $is_only = $exc->is_only('card_name', $_POST['card_name'], $_POST['id']);

                if (!$is_only) {
                    sys_msg(sprintf($GLOBALS['_LANG']['cardname_exist'], stripslashes($_POST['card_name'])), 1);
                }
            }
            $param = "card_name = '$_POST[card_name]', card_fee = '$_POST[card_fee]', free_money= $_POST[free_money], card_desc = '$_POST[card_desc]'";
            /* 处理图片 */
            $img_name = basename($image->upload_image($_FILES['card_img'], "cardimg", $_POST['old_cardimg']));
            if ($img_name) {
                $param .= "  ,card_img ='$img_name' ";
            }

            if ($exc->edit($param, $_POST['id'])) {
                admin_log($_POST['card_name'], 'edit', 'card');

                $link[0]['text'] = $GLOBALS['_LANG']['back_list'];
                $link[0]['href'] = 'card.php?act=list&' . list_link_postfix();

                $note = sprintf($GLOBALS['_LANG']['cardedit_succeed'], $_POST['card_name']);
                sys_msg($note, 0, $link);
            } else {
                die($this->db->error());
            }
        }

        /* 删除卡片图片 */
        if ($_REQUEST['act'] == 'drop_card_img') {
            /* 权限判断 */
            admin_priv('card_manage');
            $card_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

            /* 取得logo名称 */
            $sql = "SELECT card_img FROM " . $this->ecs->table('card') . " WHERE card_id = '$card_id'";
            $img_name = $this->db->getOne($sql);

            if (!empty($img_name)) {
                @unlink(ROOT_PATH . DATA_DIR . '/cardimg/' . $img_name);
                $sql = "UPDATE " . $this->ecs->table('card') . " SET card_img = '' WHERE card_id = '$card_id'";
                $this->db->query($sql);
            }
            $link = array(array('text' => $GLOBALS['_LANG']['card_edit_lnk'], 'href' => 'card.php?act=edit&id=' . $card_id), array('text' => $GLOBALS['_LANG']['card_list_lnk'], 'href' => 'brand.php?act=list'));
            sys_msg($GLOBALS['_LANG']['drop_card_img_success'], 0, $link);
        }
        /*------------------------------------------------------ */
//-- ajax编辑卡片名字
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit_card_name') {
            check_authz_json('card_manage');
            $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
            $card_name = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));

            if (!$exc->is_only('card_name', $card_name, $card_id)) {
                make_json_error(sprintf($GLOBALS['_LANG']['cardname_exist'], $card_name));
            }
            $old_card_name = $exc->get_name($card_id);
            if ($exc->edit("card_name='$card_name'", $card_id)) {
                admin_log(addslashes($old_card_name), 'edit', 'card');
                make_json_result(stripcslashes($card_name));
            } else {
                make_json_error($this->db->error());
            }
        }
        /*------------------------------------------------------ */
//-- ajax编辑卡片费用
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit_card_fee') {
            check_authz_json('card_manage');
            $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
            $card_fee = empty($_REQUEST['val']) ? 0.00 : floatval($_REQUEST['val']);

            $card_name = $exc->get_name($card_id);
            if ($exc->edit("card_fee ='$card_fee'", $card_id)) {
                admin_log(addslashes($card_name), 'edit', 'card');
                make_json_result($card_fee);
            } else {
                make_json_error($this->db->error());
            }
        }
        /*------------------------------------------------------ */
//-- ajax编辑免费额度
        /*------------------------------------------------------ */
        if ($_REQUEST['act'] == 'edit_free_money') {
            check_authz_json('card_manage');
            $card_id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
            $free_money = empty($_REQUEST['val']) ? 0.00 : floatval($_REQUEST['val']);

            $card_name = $exc->get_name($card_id);
            if ($exc->edit("free_money ='$free_money'", $card_id)) {
                admin_log(addslashes($card_name), 'edit', 'card');
                make_json_result($free_money);
            } else {
                make_json_error($this->db->error());
            }
        }
    }

    private function cards_list()
    {
        $result = get_filter();
        if ($result === false) {
            $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'card_id' : trim($_REQUEST['sort_by']);
            $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

            /* 分页大小 */
            $sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('card');
            $filter['record_count'] = $GLOBALS['db']->getOne($sql);

            $filter = page_and_size($filter);

            /* 查询 */
            $sql = "SELECT card_id, card_name, card_img, card_fee, free_money, card_desc" .
                " FROM " . $GLOBALS['ecs']->table('card') .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

            set_filter($filter, $sql);
        } else {
            $sql = $result['sql'];
            $filter = $result['filter'];
        }

        $card_list = $GLOBALS['db']->getAll($sql);

        $arr = array('card_list' => $card_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

        return $arr;
    }
}