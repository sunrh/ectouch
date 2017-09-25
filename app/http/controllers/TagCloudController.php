<?php

namespace app\http\controllers;

/**
 * 标签云
 * Class TagCloudController
 * @package app\http\controllers
 */
class TagCloudController extends Controller
{
    public function actionIndex()
    {
        assign_template();
        $position = assign_ur_here(0, $GLOBALS['_LANG']['tag_cloud']);
        $this->smarty->assign('page_title', $position['title']);    // 页面标题
        $this->smarty->assign('ur_here', $position['ur_here']);  // 当前位置
        $this->smarty->assign('categories', get_categories_tree()); // 分类树
        $this->smarty->assign('helps', get_shop_help());       // 网店帮助
        $this->smarty->assign('top_goods', get_top10());           // 销售排行
        $this->smarty->assign('promotion_info', get_promotion_info());

        /* 调查 */
        $vote = get_vote();
        if (!empty($vote)) {
            $this->smarty->assign('vote_id', $vote['id']);
            $this->smarty->assign('vote', $vote['content']);
        }

        assign_dynamic('tag_cloud');

        $tags = get_tags();

        if (!empty($tags)) {
            load_helper('clips');
            color_tag($tags);
        }

        $this->smarty->assign('tags', $tags);

        $this->smarty->display('tag_cloud.dwt');
    }
}
