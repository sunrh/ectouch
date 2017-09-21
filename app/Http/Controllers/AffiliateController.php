<?php

namespace App\Http\Controllers;

/**
 * Class AffiliateController
 * @package App\Http\Controllers
 */
class AffiliateController extends Controller
{
    public function actionIndex()
    {
        $charset = empty($_GET['charset']) ? 'UTF8' : $_GET['charset'];
        $display_mode = empty($_GET['display_mode']) ? 'javascript' : $_GET['display_mode'];

        if ($display_mode == 'javascript') {
            $charset_array = array('UTF8', 'GBK', 'gbk', 'utf8', 'GB2312', 'gb2312');
            if (!in_array($charset, $charset_array)) {
                $charset = 'UTF8';
            }
            header('content-type: application/x-javascript; charset=' . ($charset == 'UTF8' ? 'utf-8' : $charset));
        }

        $cache_id = sprintf('%X', crc32($_SERVER['QUERY_STRING']));

        $goodsid = intval($_GET['gid']);
        $userid = intval($_GET['u']);
        $type = intval($_GET['type']);

        $tpl = ROOT_PATH . DATA_DIR . '/affiliate.html';
        if (!$this->smarty->is_cached($tpl, $cache_id)) {
            $time = gmtime();

            $goods_url = $this->ecs->url() . "goods.php?u=$userid&id=";
            $goods = get_goods_info($goodsid);
            $goods['goods_thumb'] = (strpos($goods['goods_thumb'], 'http://') === false && strpos($goods['goods_thumb'], 'https://') === false) ? $this->ecs->url() . $goods['goods_thumb'] : $goods['goods_thumb'];
            $goods['goods_img'] = (strpos($goods['goods_img'], 'http://') === false && strpos($goods['goods_img'], 'https://') === false) ? $this->ecs->url() . $goods['goods_img'] : $goods['goods_img'];
            $goods['shop_price'] = price_format($goods['shop_price']);

            $this->smarty->assign('goods', $goods);
            $this->smarty->assign('userid', $userid);
            $this->smarty->assign('type', $type);

            $this->smarty->assign('url', $this->ecs->url());
            $this->smarty->assign('goods_url', $goods_url);
        }
        $output = $this->smarty->fetch($tpl, $cache_id);
        $output = str_replace("\r", '', $output);
        $output = str_replace("\n", '', $output);

        if ($display_mode == 'javascript') {
            echo "document.write('$output');";
        } elseif ($display_mode == 'iframe') {
            echo $output;
        }
    }
}
