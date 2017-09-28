<?php

namespace App\Http\Controllers;

use App\Libraries\Rss\RSSBuilder;

define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

/**
 * RSS Feed 生成程序
 * Class FeedController
 * @package App\Http\Controllers
 */
class FeedController extends Controller
{
    public function actionIndex()
    {
        header('Content-Type: application/xml; charset=' . CHARSET);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
        header('Last-Modified: ' . date('r'));
        header('Pragma: no-cache');

        $ver = isset($_REQUEST['ver']) ? $_REQUEST['ver'] : '2.00';
        $cat = isset($_REQUEST['cat']) ? ' AND ' . get_children(intval($_REQUEST['cat'])) : '';
        $brd = isset($_REQUEST['brand']) ? ' AND g.brand_id=' . intval($_REQUEST['brand']) . ' ' : '';

        $uri = $this->ecs->url();

        $rss = new RSSBuilder(CHARSET, $uri, htmlspecialchars($GLOBALS['_CFG']['shop_name']), htmlspecialchars($GLOBALS['_CFG']['shop_desc']), $uri . 'animated_favicon.gif');
        $rss->addDCdata('', 'http://www.ectouch.cn', date('r'));

        if (isset($_REQUEST['type'])) {
            if ($_REQUEST['type'] == 'group_buy') {
                $now = gmtime();
                $sql = 'SELECT act_id, act_name, act_desc, start_time ' .
                    "FROM " . $GLOBALS['ecs']->table('goods_activity') .
                    "WHERE act_type = '" . GAT_GROUP_BUY . "' " .
                    "AND start_time <= '$now' AND is_finished < 3 ORDER BY start_time DESC";
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = build_uri('group_buy', array('gbid' => $row['act_id']), $row['act_name']);
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['act_name']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = htmlspecialchars($row['act_desc']);
                        $subject = $GLOBALS['_LANG']['group_buy'];
                        $date = local_date('r', $row['start_time']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }

            if ($_REQUEST['type'] == 'snatch') {
                $now = gmtime();
                $sql = 'SELECT act_id, act_name, act_desc, start_time ' .
                    "FROM " . $GLOBALS['ecs']->table('goods_activity') .
                    "WHERE act_type = '" . GAT_SNATCH . "' " .
                    "AND start_time <= '$now' AND is_finished < 3 ORDER BY start_time DESC";
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = build_uri('snatch', array('sid' => $row['act_id']), $row['act_name']);
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['act_name']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = htmlspecialchars($row['act_desc']);
                        $subject = $GLOBALS['_LANG']['snatch'];
                        $date = local_date('r', $row['start_time']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }

            if ($_REQUEST['type'] == 'auction') {
                $now = gmtime();
                $sql = 'SELECT act_id, act_name, act_desc, start_time ' .
                    "FROM " . $GLOBALS['ecs']->table('goods_activity') .
                    "WHERE act_type = '" . GAT_AUCTION . "' " .
                    "AND start_time <= '$now' AND is_finished < 3 ORDER BY start_time DESC";
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = build_uri('auction', array('auid' => $row['act_id']), $row['act_name']);
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['act_name']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = htmlspecialchars($row['act_desc']);
                        $subject = $GLOBALS['_LANG']['auction'];
                        $date = local_date('r', $row['start_time']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }

            if ($_REQUEST['type'] == 'exchange') {
                $sql = 'SELECT g.goods_id, g.goods_name, g.goods_brief, g.last_update ' .
                    "FROM " . $GLOBALS['ecs']->table('exchange_goods') . " AS eg, " .
                    $GLOBALS['ecs']->table('goods') . " AS g " .
                    "WHERE eg.goods_id = g.goods_id";
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = build_uri('exchange_goods', array('gid' => $row['goods_id']), $row['goods_name']);
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['goods_name']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = htmlspecialchars($row['goods_brief']);
                        $subject = $GLOBALS['_LANG']['exchange'];
                        $date = local_date('r', $row['last_update']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }

            if ($_REQUEST['type'] == 'activity') {
                $now = gmtime();
                $sql = 'SELECT act_id, act_name, start_time ' .
                    "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
                    " WHERE start_time <= '$now' AND end_time >= '$now'";
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = 'activity.php';
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['act_name']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = '';
                        $subject = $GLOBALS['_LANG']['favourable'];
                        $date = local_date('r', $row['start_time']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }

            if ($_REQUEST['type'] == 'package') {
                $now = gmtime();
                $sql = 'SELECT act_id, act_name, act_desc, start_time ' .
                    "FROM " . $GLOBALS['ecs']->table('goods_activity') .
                    "WHERE act_type = '" . GAT_PACKAGE . "' " .
                    "AND start_time <= '$now' AND is_finished < 3 ORDER BY start_time DESC";
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = 'package.php';
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['act_name']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = htmlspecialchars($row['act_desc']);
                        $subject = $GLOBALS['_LANG']['remark_package'];
                        $date = local_date('r', $row['start_time']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }

            if (substr($_REQUEST['type'], 0, 11) == 'article_cat') {
                $sql = 'SELECT article_id, title, author, add_time' .
                    ' FROM ' . $GLOBALS['ecs']->table('article') .
                    ' WHERE is_open = 1 AND ' . get_article_children(substr($_REQUEST['type'], 11)) .
                    ' ORDER BY add_time DESC LIMIT ' . $GLOBALS['_CFG']['article_page_size'];
                $res = $this->db->query($sql);

                if ($res !== false) {
                    foreach ($res as $row) {
                        $item_url = build_uri('article', array('aid' => $row['article_id']), $row['title']);
                        $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                        $about = $uri . $item_url;
                        $title = htmlspecialchars($row['title']);
                        $link = $uri . $item_url . $separator . 'from=rss';
                        $desc = '';
                        $subject = htmlspecialchars($row['author']);
                        $date = local_date('r', $row['add_time']);

                        $rss->addItem($about, $title, $link, $desc, $subject, $date);
                    }

                    $rss->outputRSS($ver);
                }
            }
        } else {
            $in_cat = $cat > 0 ? ' AND ' . get_children($cat) : '';

            $sql = 'SELECT c.cat_name, g.goods_id, g.goods_name, g.goods_brief, g.last_update ' .
                'FROM ' . $this->ecs->table('category') . ' AS c, ' . $this->ecs->table('goods') . ' AS g ' .
                'WHERE c.cat_id = g.cat_id AND g.is_delete = 0 AND g.is_alone_sale = 1 ' . $brd . $cat .
                'ORDER BY g.last_update DESC';
            $res = $this->db->query($sql);

            if ($res !== false) {
                foreach ($res as $row) {
                    $item_url = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
                    $separator = (strpos($item_url, '?') === false) ? '?' : '&amp;';
                    $about = $uri . $item_url;
                    $title = htmlspecialchars($row['goods_name']);
                    $link = $uri . $item_url . $separator . 'from=rss';
                    $desc = htmlspecialchars($row['goods_brief']);
                    $subject = htmlspecialchars($row['cat_name']);
                    $date = local_date('r', $row['last_update']);

                    $rss->addItem($about, $title, $link, $desc, $subject, $date);
                }

                $rss->outputRSS($ver);
            }
        }
    }
}
