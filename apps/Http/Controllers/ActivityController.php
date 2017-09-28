<?php

namespace App\Http\Controllers;

/**
 * 优惠活动
 * Class ActivityController
 * @package App\Http\Controllers
 */
class ActivityController extends Controller
{
    public function actionIndex()
    {
        load_helper(['order', 'transaction']);
        load_lang(['flow', 'user']);

        assign_template();
        assign_dynamic('activity');
        $position = assign_ur_here(0, $GLOBALS['_LANG']['shopping_activity']);
        $this->smarty->assign('page_title', $position['title']);    // 页面标题
        $this->smarty->assign('ur_here', $position['ur_here']);  // 当前位置

        /* 取得用户等级 */
        $user_rank_list = array();
        $user_rank_list[0] = $GLOBALS['_LANG']['not_user'];
        $sql = "SELECT rank_id, rank_name FROM " . $this->ecs->table('user_rank');
        $res = $this->db->query($sql);
        foreach ($res as $row) {
            $user_rank_list[$row['rank_id']] = $row['rank_name'];
        }

        $sql = "SELECT * FROM " . $this->ecs->table('favourable_activity') . " ORDER BY `sort_order` ASC,`end_time` DESC";
        $res = $this->db->query($sql);

        $list = array();
        foreach ($res as $row) {
            $row['start_time'] = local_date('Y-m-d H:i', $row['start_time']);
            $row['end_time'] = local_date('Y-m-d H:i', $row['end_time']);

            //享受优惠会员等级
            $user_rank = explode(',', $row['user_rank']);
            $row['user_rank'] = array();
            foreach ($user_rank as $val) {
                if (isset($user_rank_list[$val])) {
                    $row['user_rank'][] = $user_rank_list[$val];
                }
            }

            //优惠范围类型、内容
            if ($row['act_range'] != FAR_ALL && !empty($row['act_range_ext'])) {
                if ($row['act_range'] == FAR_CATEGORY) {
                    $row['act_range'] = $GLOBALS['_LANG']['far_category'];
                    $row['program'] = 'category.php?id=';
                    $sql = "SELECT cat_id AS id, cat_name AS name FROM " . $this->ecs->table('category') .
                        " WHERE cat_id " . db_create_in($row['act_range_ext']);
                } elseif ($row['act_range'] == FAR_BRAND) {
                    $row['act_range'] = $GLOBALS['_LANG']['far_brand'];
                    $row['program'] = 'brand.php?id=';
                    $sql = "SELECT brand_id AS id, brand_name AS name FROM " . $this->ecs->table('brand') .
                        " WHERE brand_id " . db_create_in($row['act_range_ext']);
                } else {
                    $row['act_range'] = $GLOBALS['_LANG']['far_goods'];
                    $row['program'] = 'goods.php?id=';
                    $sql = "SELECT goods_id AS id, goods_name AS name FROM " . $this->ecs->table('goods') .
                        " WHERE goods_id " . db_create_in($row['act_range_ext']);
                }
                $act_range_ext = $this->db->getAll($sql);
                $row['act_range_ext'] = $act_range_ext;
            } else {
                $row['act_range'] = $GLOBALS['_LANG']['far_all'];
            }

            //优惠方式
            switch ($row['act_type']) {
                case 0:
                    $row['act_type'] = $GLOBALS['_LANG']['fat_goods'];
                    $row['gift'] = unserialize($row['gift']);
                    if (is_array($row['gift'])) {
                        foreach ($row['gift'] as $k => $v) {
                            $row['gift'][$k]['thumb'] = get_image_path($this->db->getOne("SELECT goods_thumb FROM " . $this->ecs->table('goods') . " WHERE goods_id = '" . $v['id'] . "'"));
                        }
                    }
                    break;
                case 1:
                    $row['act_type'] = $GLOBALS['_LANG']['fat_price'];
                    $row['act_type_ext'] .= $GLOBALS['_LANG']['unit_yuan'];
                    $row['gift'] = array();
                    break;
                case 2:
                    $row['act_type'] = $GLOBALS['_LANG']['fat_discount'];
                    $row['act_type_ext'] .= "%";
                    $row['gift'] = array();
                    break;
            }

            $list[] = $row;
        }

        $this->smarty->assign('list', $list);
        $this->smarty->assign('helps', get_shop_help());       // 网店帮助
        $this->smarty->assign('lang', $GLOBALS['_LANG']);
        $this->smarty->assign('feed_url', ($GLOBALS['_CFG']['rewrite'] == 1) ? "feed-typeactivity.xml" : 'feed.php?type=activity'); // RSS URL

        $this->smarty->display('activity.dwt');
    }
}
