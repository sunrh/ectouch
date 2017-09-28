<?php

namespace App\Http\Controllers;

/**
 * 选购中心
 * Class PickOutController
 * @package App\Http\Controllers
 */
class PickOutController extends Controller
{
    public function actionIndex()
    {
        $condition = array();
        $picks = array();
        $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
        if (!empty($_GET['attr'])) {
            foreach ($_GET['attr'] as $key => $value) {
                if (!is_numeric($key)) {
                    unset($_GET['attr'][$key]);
                    continue;
                }
                $key = intval($key);
                $_GET['attr'][$key] = htmlspecialchars($value);
            }
        }

        if (empty($cat_id)) {
            /* 获取所有符合条件的商品类型 */
            $sql = "SELECT DISTINCT t.cat_id, t.cat_name " .
                "FROM " . $this->ecs->table('goods_type') . " AS t, " . $this->ecs->table('attribute') . " AS a, " . $this->ecs->table('goods_attr') . " AS g " .
                "WHERE t.cat_id = a.cat_id AND a.attr_id = g.attr_id AND t.enabled = 1";
            $rs = $this->db->query($sql);

            $in_cat = array();
            $cat_name = array();
            $in_goods = '';

            foreach ($rs as $row) {
                $condition[$row['cat_id']]['name'] = $row['cat_name'];
                $in_cat[] = $row['cat_id'];
            }

            $in_cat = "AND a.cat_id " . db_create_in($in_cat);

            /* 获取符合条件的属性 */
            $sql = "SELECT DISTINCT a.attr_id " .
                "FROM " . $this->ecs->table('goods_attr') . " AS g, " . $this->ecs->table('attribute') . " AS a " .
                "WHERE a.attr_id = g.attr_id " . $in_cat;

            $in_attr = $this->db->getCol($sql); //符合条件attr_id;
            $in_attr = 'AND g.attr_id ' . db_create_in($in_attr);

            /* 获取所有属性值 */
            $sql = "SELECT DISTINCT g.attr_id, a.attr_name, a.cat_id, g.attr_value" .
                " FROM " . $this->ecs->table('goods_attr') . " AS g, " .
                $this->ecs->table('attribute') . " AS a" .
                " WHERE a.attr_id = g.attr_id " . $in_attr . " ORDER BY cat_id";
            $rs = $this->db->query($sql);

            foreach ($rs as $row) {
                if (empty($condition[$row['cat_id']]['cat'][$row['attr_id']]['cat_name'])) {
                    $condition[$row['cat_id']]['cat'][$row['attr_id']]['cat_name'] = $row['attr_name'];
                }

                $condition[$row['cat_id']]['cat'][$row['attr_id']]['list'][] = array('name' => $row['attr_value'], 'url' => 'pick_out.php?cat_id=' . $row['cat_id'] . '&amp;attr[' . $row['attr_id'] . ']=' . urlencode($row['attr_value']));
            }

            /* 获取商品总数 */
            $goods_count = $this->db->GetOne("SELECT COUNT(DISTINCT(goods_id)) FROM " . $this->ecs->table('goods_attr'));
            /* 获取符合条件的商品id */
            //$sql = "SELECT DISTINCT goods_id FROM " .$this->ecs->table('goods_attr'). " LIMIT 100";
            $sql = "SELECT DISTINCT goods_id FROM " . $this->ecs->table('goods_attr');
            $in_goods = $this->db->GetCol($sql);
            $in_goods = 'AND g.goods_id ' . db_create_in(implode(',', $in_goods));
            $url = "search.php?pickout=1";
        } else {
            /* 取得商品类型名称 */
            $sql = "SELECT cat_name FROM " . $this->ecs->table('goods_type') . " WHERE cat_id = '$cat_id'";
            $cat_name = $this->db->GetOne($sql);
            $condition[0]['name'] = $cat_name;

            $picks[] = array('name' => '<strong>' . $GLOBALS['_LANG']['goods_type'] . ':</strong><br />' . $cat_name, 'url' => 'pick_out.php');

            $attr_picks = array(); //选择过的attr_id

            /* 处理属性,获取满足属性的goods_id */
            if (!empty($_GET['attr'])) {
                $attr_table = '';
                $attr_where = '';
                $attr_url = '';
                $i = 0;
                $goods_result = '';
                foreach ($_GET['attr'] as $key => $value) {
                    $attr_url .= '&attr[' . $key . ']=' . $value;

                    $attr_picks[] = $key;
                    if ($i > 0) {
                        if (empty($goods_result)) {
                            break;
                        }
                        $goods_result = $this->db->getCol("SELECT goods_id FROM " . $this->ecs->table("goods_attr") . " WHERE goods_id IN (" . implode(',', $goods_result) . ") AND attr_id='$key' AND attr_value='$value'");
                    } else {
                        $goods_result = $this->db->getCol("SELECT goods_id FROM " . $this->ecs->table("goods_attr") . " WHERE attr_id='$key' AND attr_value='$value'");
                    }
                    $i++;
                }

                /* 获取指定attr_id的名字 */
                $sql = "SELECT attr_id, attr_name FROM " . $this->ecs->table('attribute') . " WHERE attr_id " . db_create_in(implode(',', $attr_picks));
                $rs = $this->db->query($sql);
                foreach ($rs as $row) {
                    $picks[] = array('name' => '<strong>' . $row['attr_name'] . ':</strong><br />' . htmlspecialchars(urldecode($_GET['attr'][$row['attr_id']])), 'url' => 'pick_out.php?cat_id=' . $cat_id . $this->search_url($attr_picks, $row['attr_id']));
                }

                /* 查出数量 */
                $goods_count = count($goods_result);
                /* 获取符合条件的goods_id */
                $in_goods = 'AND g.goods_id ' . db_create_in(implode(',', $goods_result));
            } else {
                /* 仅选择了商品类型的情况 */

                /* 查出数量 */
                $goods_count = $this->db->GetOne("SELECT COUNT(distinct(g.goods_id)) FROM " . $this->ecs->table('goods_attr') . " AS g, " . $this->ecs->table('attribute') . " AS a WHERE g.attr_id = a.attr_id AND a.cat_id = '$cat_id' ");

                /* 防止结果过大，最多只查出前100个goods_id */

                $sql = "SELECT DISTINCT g.goods_id FROM " . $this->ecs->table('goods_attr') . " AS g, " . $this->ecs->table('attribute') . " AS a WHERE g.attr_id = a.attr_id AND a.cat_id = '$cat_id' LIMIT 100";
                $in_goods = $this->db->GetCol($sql);
                $in_goods = 'AND g.goods_id ' . db_create_in(implode(',', $in_goods));
            }

            /* 获取符合条件的属性 */
            $sql = "SELECT DISTINCT a.attr_id FROM " . $this->ecs->table('goods_attr') . " AS g, " . $this->ecs->table('attribute') . " AS a " .
                "WHERE a.attr_id = g.attr_id " . $in_goods;

            $in_attr = $this->db->GetCol($sql); // 符合条件attr_id;
            $in_attr = array_diff($in_attr, $attr_picks); // 除去已经选择过的attr_id
            $in_attr = 'AND g.attr_id ' . db_create_in(implode(',', $in_attr));

            /* 获取所有属性值 */
            $sql = "SELECT DISTINCT g.attr_id, a.attr_name, g.attr_value FROM " . $this->ecs->table('goods_attr') . " AS g, " . $this->ecs->table('attribute') . " AS a WHERE a.attr_id = g.attr_id " . $in_attr . $in_goods;
            $rs = $this->db->query($sql);

            foreach ($rs as $row) {
                if (empty($condition[0]['cat'][$row['attr_id']]['cat_name'])) {
                    $condition[0]['cat'][$row['attr_id']]['cat_name'] = $row['attr_name'];
                }
                $condition[0]['cat'][$row['attr_id']]['list'][] = array('name' => $row['attr_value'], 'url' => 'pick_out.php?cat_id=' . $cat_id . $this->search_url($attr_picks) . '&amp;attr[' . $row['attr_id'] . ']=' . urlencode($row['attr_value']));
            }

            /* 生成更多商品的url */
            $url = "search.php?pickout=1&amp;cat_id=" . $cat_id . $this->search_url($attr_picks);
        }

        /* 显示商品 */
        $goods = array();
        $sql = "SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, " .
            "IFNULL(mp.user_price, g.shop_price * '". session('discount') ."') AS shop_price, " .
            "g.promote_price, promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb " .
            "FROM " . $this->ecs->table('goods') . " AS g " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '". session('user_rank') ."' " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " . $in_goods .
            "ORDER BY g.sort_order, g.last_update DESC";
        $res = $this->db->selectLimit($sql, 4);

        /* 获取品牌 */
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(g.goods_id) AS goods_num " .
            " FROM " . $this->ecs->table('goods') . " AS g " .
            " LEFT JOIN " . $this->ecs->table('brand') . " AS b ON g.brand_id=b.brand_id " .
            " WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND b.brand_id > 0 " . $in_goods .
            " GROUP BY g.brand_id ";

        $brand_list = $this->db->getAll($sql);
        foreach ($brand_list as $key => $val) {
            $brand_list[$key]['url'] = $url . '&amp;brand=' . $val['brand_id'];
        }

        /* 获取分类 */
        $sql = "SELECT c.cat_id, c.cat_name, COUNT(g.goods_id) AS goods_num " .
            " FROM " . $this->ecs->table('goods') . " AS g " .
            " LEFT JOIN " . $this->ecs->table('category') . " AS c ON c.cat_id = g.cat_id " .
            " WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0  " . $in_goods .
            " GROUP BY g.cat_id ";

        $cat_list = $this->db->getAll($sql);

        foreach ($cat_list as $key => $val) {
            $cat_list[$key]['url'] = $url . '&amp;category=' . $val['cat_id'];
        }


        $idx = 0;
        foreach ($res as $row) {
            if ($row['promote_price'] > 0) {
                $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            } else {
                $promote_price = 0;
            }

            $goods[$idx]['id'] = $row['goods_id'];
            $goods[$idx]['name'] = $row['goods_name'];
            $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['market_price'] = $row['market_price'];
            $goods[$idx]['shop_price'] = price_format($row['shop_price']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
            $goods[$idx]['brief'] = $row['goods_brief'];
            $goods[$idx]['thumb'] = get_image_path($row['goods_thumb']);
            $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

            $idx++;
        }

        $picks[] = array('name' => $GLOBALS['_LANG']['remove_all'], 'url' => 'pick_out.php');

        assign_template();
        $position = assign_ur_here(0, $GLOBALS['_LANG']['pick_out']);
        $this->smarty->assign('page_title', $position['title']);    // 页面标题
        $this->smarty->assign('ur_here', $position['ur_here']);  // 当前位置

        $this->smarty->assign('brand_list', $brand_list);       //品牌
        $this->smarty->assign('cat_list', $cat_list);        //分类列表

        $this->smarty->assign('categories', get_categories_tree()); // 分类树
        $this->smarty->assign('helps', get_shop_help());  // 网店帮助
        $this->smarty->assign('top_goods', get_top10());      // 销售排行
        $this->smarty->assign('data_dir', DATA_DIR);  // 数据目录

        /* 调查 */
        $vote = get_vote();
        if (!empty($vote)) {
            $this->smarty->assign('vote_id', $vote['id']);
            $this->smarty->assign('vote', $vote['content']);
        }

        assign_dynamic('pick_out');

        $this->smarty->assign('url', $url);
        $this->smarty->assign('pickout_goods', $goods);
        $this->smarty->assign('count', $goods_count);
        $this->smarty->assign('picks', $picks);
        $this->smarty->assign('condition', $condition);
        $this->smarty->display('pick_out.dwt');
    }

    /**
     *  生成搜索的链接地址
     *
     * @access  public
     * @param   int        attr_id        要排除的attr_id
     *
     * @return string
     */
    private function search_url(&$attr_picks, $attr_id = 0)
    {
        $str = '';
        foreach ($attr_picks as $pick_id) {
            if ($pick_id != $attr_id) {
                $str .= '&amp;attr[' . $pick_id . ']=' . urlencode($_GET['attr'][$pick_id]);
            }
        }

        return $str;
    }
}
