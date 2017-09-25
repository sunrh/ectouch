<?php

namespace app\http\controllers;

/**
 * 商品比较程序
 * Class CompareController
 * @package app\http\controllers
 */
class CompareController extends Controller
{
    public function actionIndex()
    {
        if (!empty($_REQUEST['goods']) && is_array($_REQUEST['goods']) && count($_REQUEST['goods']) > 1) {
            foreach ($_REQUEST['goods'] as $key => $val) {
                $_REQUEST['goods'][$key] = intval($val);
            }

            $where = db_create_in($_REQUEST['goods'], 'id_value');
            $sql = "SELECT id_value , AVG(comment_rank) AS cmt_rank, COUNT(*) AS cmt_count" .
                " FROM " . $this->ecs->table('comment') .
                " WHERE $where AND comment_type = 0" .
                ' GROUP BY id_value ';
            $query = $this->db->query($sql);
            $cmt = array();
            foreach ($query as $row) {
                $cmt[$row['id_value']] = $row;
            }

            $where = db_create_in($_REQUEST['goods'], 'g.goods_id');
            $sql = "SELECT g.goods_id, g.goods_type, g.goods_name, g.shop_price, g.goods_weight, g.goods_thumb, g.goods_brief, " .
                "a.attr_name, v.attr_value, a.attr_id, b.brand_name, " .
                "IFNULL(mp.user_price, g.shop_price * '". session('discount') ."') AS rank_price " .
                "FROM " . $this->ecs->table('goods') . " AS g " .
                "LEFT JOIN " . $this->ecs->table('goods_attr') . " AS v ON v.goods_id = g.goods_id " .
                "LEFT JOIN " . $this->ecs->table('attribute') . " AS a ON a.attr_id = v.attr_id " .
                "LEFT JOIN " . $this->ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
                "LEFT JOIN " . $this->ecs->table('member_price') . " AS mp " .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '". session('user_rank') ."' " .
                "WHERE g.is_delete = 0 AND $where " .
                "ORDER BY a.attr_id";
            $res = $this->db->query($sql);
            $arr = array();
            $ids = $_REQUEST['goods'];
            $attr_name = array();
            $type_id = 0;
            foreach ($res as $row) {
                $goods_id = $row['goods_id'];
                $type_id = $row['goods_type'];
                $arr[$goods_id]['goods_id'] = $goods_id;
                $arr[$goods_id]['url'] = build_uri('goods', array('gid' => $goods_id), $row['goods_name']);
                $arr[$goods_id]['goods_name'] = $row['goods_name'];
                $arr[$goods_id]['shop_price'] = price_format($row['shop_price']);
                $arr[$goods_id]['rank_price'] = price_format($row['rank_price']);
                $arr[$goods_id]['goods_weight'] = (intval($row['goods_weight']) > 0) ?
                    ceil($row['goods_weight']) . $GLOBALS['_LANG']['kilogram'] : ceil($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];
                $arr[$goods_id]['goods_thumb'] = get_image_path($row['goods_thumb']);
                $arr[$goods_id]['goods_brief'] = $row['goods_brief'];
                $arr[$goods_id]['brand_name'] = $row['brand_name'];

                $arr[$goods_id]['properties'][$row['attr_id']]['name'] = $row['attr_name'];
                if (!empty($arr[$goods_id]['properties'][$row['attr_id']]['value'])) {
                    $arr[$goods_id]['properties'][$row['attr_id']]['value'] .= ',' . $row['attr_value'];
                } else {
                    $arr[$goods_id]['properties'][$row['attr_id']]['value'] = $row['attr_value'];
                }

                if (!isset($arr[$goods_id]['comment_rank'])) {
                    $arr[$goods_id]['comment_rank'] = isset($cmt[$goods_id]) ? ceil($cmt[$goods_id]['cmt_rank']) : 0;
                    $arr[$goods_id]['comment_number'] = isset($cmt[$goods_id]) ? $cmt[$goods_id]['cmt_count'] : 0;
                    $arr[$goods_id]['comment_number'] = sprintf($GLOBALS['_LANG']['comment_num'], $arr[$goods_id]['comment_number']);
                }

                $tmp = $ids;
                $key = array_search($goods_id, $tmp);

                if ($key !== null && $key !== false) {
                    unset($tmp[$key]);
                }

                $arr[$goods_id]['ids'] = !empty($tmp) ? "goods[]=" . implode('&amp;goods[]=', $tmp) : '';
            }

            $sql = "SELECT attr_id,attr_name FROM " . $this->ecs->table('attribute') . " WHERE cat_id='$type_id' ORDER BY attr_id";

            $attribute = array();

            $query = $this->db->query($sql);
            foreach ($query as $rt) {
                $attribute[$rt['attr_id']] = $rt['attr_name'];
            }

            $this->smarty->assign('attribute', $attribute);
            $this->smarty->assign('goods_list', $arr);
        } else {
            show_message($GLOBALS['_LANG']['compare_no_goods']);
            exit;
        }

        assign_template();
        $position = assign_ur_here(0, $GLOBALS['_LANG']['goods_compare']);
        $this->smarty->assign('page_title', $position['title']);    // 页面标题
        $this->smarty->assign('ur_here', $position['ur_here']);  // 当前位置

        $this->smarty->assign('categories', get_categories_tree()); // 分类树
        $this->smarty->assign('helps', get_shop_help());       // 网店帮助

        assign_dynamic('compare');

        $this->smarty->display('compare.dwt');
    }
}
