<?php

namespace App\Http\Controllers;

/**
 * 专题页面
 * Class TopicController
 * @package App\Http\Controllers
 */
class TopicController extends Controller
{
    public function actionIndex()
    {
        $topic_id = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);

        $sql = "SELECT template FROM " . $this->ecs->table('topic') .
            "WHERE topic_id = '$topic_id' and  " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";

        $topic = $this->db->getRow($sql);

        if (empty($topic)) {
            /* 如果没有找到任何记录则跳回到首页 */
            ecs_header("Location: ./\n");
            exit;
        }

        $templates = empty($topic['template']) ? 'topic.dwt' : $topic['template'];

        $cache_id = sprintf('%X', crc32(session('user_rank') . '-' . $GLOBALS['_CFG']['lang'] . '-' . $topic_id));

        if (!$this->smarty->is_cached($templates, $cache_id)) {
            $sql = "SELECT * FROM " . $this->ecs->table('topic') . " WHERE topic_id = '$topic_id'";

            $topic = $this->db->getRow($sql);
            $topic['data'] = addcslashes($topic['data'], "'");
            $tmp = @unserialize($topic["data"]);
            $arr = (array)$tmp;

            $goods_id = array();

            foreach ($arr as $key => $value) {
                foreach ($value as $k => $val) {
                    $opt = explode('|', $val);
                    $arr[$key][$k] = $opt[1];
                    $goods_id[] = $opt[1];
                }
            }

            $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '". session('discount') ."') AS shop_price, g.promote_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '". session('user_rank') ."' " .
                "WHERE " . db_create_in($goods_id, 'g.goods_id');

            $res = $GLOBALS['db']->query($sql);

            $sort_goods_arr = array();

            foreach ($res as $row) {
                if ($row['promote_price'] > 0) {
                    $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
                    $row['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
                } else {
                    $row['promote_price'] = '';
                }

                if ($row['shop_price'] > 0) {
                    $row['shop_price'] = price_format($row['shop_price']);
                } else {
                    $row['shop_price'] = '';
                }

                $row['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
                $row['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
                $row['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
                $row['goods_thumb'] = get_image_path($row['goods_thumb']);
                $row['short_style_name'] = add_style($row['short_name'], $row['goods_name_style']);

                foreach ($arr as $key => $value) {
                    foreach ($value as $val) {
                        if ($val == $row['goods_id']) {
                            $key = $key == 'default' ? $GLOBALS['_LANG']['all_goods'] : $key;
                            $sort_goods_arr[$key][] = $row;
                        }
                    }
                }
            }

            /* 模板赋值 */
            assign_template();
            $position = assign_ur_here();
            $this->smarty->assign('page_title', $position['title']);       // 页面标题
            $this->smarty->assign('ur_here', $position['ur_here'] . '> ' . $topic['title']);     // 当前位置
            $this->smarty->assign('show_marketprice', $GLOBALS['_CFG']['show_marketprice']);
            $this->smarty->assign('sort_goods_arr', $sort_goods_arr);          // 商品列表
            $this->smarty->assign('topic', $topic);                   // 专题信息
            $this->smarty->assign('keywords', $topic['keywords']);       // 专题信息
            $this->smarty->assign('description', $topic['description']);    // 专题信息
            $this->smarty->assign('title_pic', $topic['title_pic']);      // 分类标题图片地址
            $this->smarty->assign('base_style', '#' . $topic['base_style']);     // 基本风格样式颜色

            $template_file = empty($topic['template']) ? 'topic.dwt' : $topic['template'];
        }
        /* 显示模板 */
        $this->smarty->display($templates, $cache_id);
    }
}
