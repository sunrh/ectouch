<?php

namespace App\Http\Controllers;

use App\Libraries\Sitemap;

/**
 * 网站地图
 * Class SitemapsController
 * @package App\Http\Controllers
 */
class SitemapsController extends Controller
{
    public function actionIndex()
    {
        define('IN_ECS', true);
        define('INIT_NO_USERS', true);
        define('INIT_NO_SMARTY', true);

        if (file_exists(ROOT_PATH . DATA_DIR . '/sitemap.dat') && time() - filemtime(ROOT_PATH . DATA_DIR . '/sitemap.dat') < 86400) {
            $out = file_get_contents(ROOT_PATH . DATA_DIR . '/sitemap.dat');
        } else {
            $site_url = rtrim($this->ecs->url(), '/');
            $sitemap = new Sitemap();
            $config = unserialize($GLOBALS['_CFG']['sitemap']);
            $item = array(
                'loc' => "$site_url/",
                'lastmod' => local_date('Y-m-d'),
                'changefreq' => $config['homepage_changefreq'],
                'priority' => $config['homepage_priority'],
            );
            $sitemap->item($item);
            /* 商品分类 */
            $sql = "SELECT cat_id,cat_name FROM " . $this->ecs->table('category') . " ORDER BY parent_id";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $item = array(
                    'loc' => "$site_url/" . build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']),
                    'lastmod' => local_date('Y-m-d'),
                    'changefreq' => $config['category_changefreq'],
                    'priority' => $config['category_priority'],
                );
                $sitemap->item($item);
            }
            /* 文章分类 */
            $sql = "SELECT cat_id,cat_name FROM " . $this->ecs->table('article_cat') . " WHERE cat_type=1";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $item = array(
                    'loc' => "$site_url/" . build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']),
                    'lastmod' => local_date('Y-m-d'),
                    'changefreq' => $config['category_changefreq'],
                    'priority' => $config['category_priority'],
                );
                $sitemap->item($item);
            }
            /* 商品 */
            $sql = "SELECT goods_id, goods_name, last_update FROM " . $this->ecs->table('goods') . " WHERE is_delete = 0 LIMIT 300";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $item = array(
                    'loc' => "$site_url/" . build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']),
                    'lastmod' => local_date('Y-m-d', $row['last_update']),
                    'changefreq' => $config['content_changefreq'],
                    'priority' => $config['content_priority'],
                );
                $sitemap->item($item);
            }
            /* 文章 */
            $sql = "SELECT article_id,title,file_url,open_type, add_time FROM " . $this->ecs->table('article') . " WHERE is_open=1";
            $res = $this->db->query($sql);

            foreach ($res as $row) {
                $article_url = $row['open_type'] != 1 ? build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
                $item = array(
                    'loc' => "$site_url/" . $article_url,
                    'lastmod' => local_date('Y-m-d', $row['add_time']),
                    'changefreq' => $config['content_changefreq'],
                    'priority' => $config['content_priority'],
                );
                $sitemap->item($item);
            }
            $out = $sitemap->generate();
            file_put_contents(ROOT_PATH . DATA_DIR . '/sitemap.dat', $out);
        }
        if (function_exists('gzencode')) {
            header('Content-type: application/x-gzip');
            $out = gzencode($out, 9);
        } else {
            header('Content-type: application/xml; charset=utf-8');
        }
        die($out);
    }
}


