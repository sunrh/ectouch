<?php

namespace app\modules\admin\controllers;

/**
 * Class SitemapController
 * @package app\modules\admin\controllers
 */
class SitemapController extends Controller
{
    public function actionIndex()
    {

        /**
         *  站点地图生成程序
         */


        /* 检查权限 */
        admin_priv('sitemap');

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            /*------------------------------------------------------ */
            //-- 设置更新频率
            /*------------------------------------------------------ */

            $config = unserialize($GLOBALS['_CFG']['sitemap']);
            $this->smarty->assign('config', $config);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['sitemap']);
            $this->smarty->assign('arr_changefreq', array(1, 0.9, 0.8, 0.7, 0.6, 0.5, 0.4, 0.3, 0.2, 0.1));
            $this->smarty->display('sitemap.htm');
        } else {
            /*------------------------------------------------------ */
            //-- 生成站点地图
            /*------------------------------------------------------ */
            // include_once('includes/cls_phpzip.php');
            // include_once('includes/cls_google_sitemap.php');

            $domain = $this->ecs->url();
            $today = local_date('Y-m-d');

            $sm =& new google_sitemap();
            $smi =& new google_sitemap_item($domain, $today, $_POST['homepage_changefreq'], $_POST['homepage_priority']);
            $sm->add_item($smi);

            $config = array(
                'homepage_changefreq' => $_POST['homepage_changefreq'],
                'homepage_priority' => $_POST['homepage_priority'],
                'category_changefreq' => $_POST['category_changefreq'],
                'category_priority' => $_POST['category_priority'],
                'content_changefreq' => $_POST['content_changefreq'],
                'content_priority' => $_POST['content_priority'],
            );
            $config = serialize($config);

            $this->db->query("UPDATE " . $this->ecs->table('shop_config') . " SET VALUE='$config' WHERE code='sitemap'");

            /* 商品分类 */
            $sql = "SELECT cat_id,cat_name FROM " . $this->ecs->table('category') . " ORDER BY parent_id";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $smi =& new google_sitemap_item($domain . build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']), $today,
                    $_POST['category_changefreq'], $_POST['category_priority']);
                $sm->add_item($smi);
            }

            /* 文章分类 */
            $sql = "SELECT cat_id,cat_name FROM " . $this->ecs->table('article_cat') . " WHERE cat_type=1";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $smi =& new google_sitemap_item($domain . build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']), $today,
                    $_POST['category_changefreq'], $_POST['category_priority']);
                $sm->add_item($smi);
            }

            /* 商品 */
            $sql = "SELECT goods_id, goods_name FROM " . $this->ecs->table('goods') . " WHERE is_delete = 0";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $smi =& new google_sitemap_item($domain . build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']), $today,
                    $_POST['content_changefreq'], $_POST['content_priority']);
                $sm->add_item($smi);
            }

            /* 文章 */
            $sql = "SELECT article_id,title,file_url,open_type FROM " . $this->ecs->table('article') . " WHERE is_open=1";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $article_url = $row['open_type'] != 1 ? build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
                $smi =& new google_sitemap_item($domain . $article_url,
                    $today, $_POST['content_changefreq'], $_POST['content_priority']);
                $sm->add_item($smi);
            }

            clear_cache_files();    // 清除缓存

            $sm_file = '../sitemaps.xml';
            if ($sm->build($sm_file)) {
                sys_msg(sprintf($GLOBALS['_LANG']['generate_success'], $this->ecs->url() . "sitemaps.xml"));
            } else {
                $sm_file = '../' . DATA_DIR . '/sitemaps.xml';
                if ($sm->build($sm_file)) {
                    sys_msg(sprintf($GLOBALS['_LANG']['generate_success'], $this->ecs->url() . DATA_DIR . '/sitemaps.xml'));
                } else {
                    sys_msg(sprintf($GLOBALS['_LANG']['generate_failed']));
                }
            }
        }
    }
}