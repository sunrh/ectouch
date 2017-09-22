<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Transport;

/**
 * Class IndexController
 * @package App\Modules\Admin\Controllers
 */
class IndexController extends Controller
{
    public function actionIndex()
    {
        load_helper('order');

        /**
         * 控制台首页
         */
        if ($_REQUEST['act'] == 'list') {
            load_helper(['menu', 'priv'], 'admin');

            $modules = $GLOBALS['modules'];
            $purview = $GLOBALS['purview'];

            foreach ($modules as $key => $value) {
                ksort($modules[$key]);
            }
            ksort($modules);

            foreach ($modules as $key => $val) {
                $menus[$key]['label'] = $GLOBALS['_LANG'][$key];
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        if (isset($purview[$k])) {
                            if (is_array($purview[$k])) {
                                $boole = false;
                                foreach ($purview[$k] as $action) {
                                    $boole = $boole || admin_priv($action, '', false);
                                }
                                if (!$boole) {
                                    continue;
                                }
                            } else {
                                if (!admin_priv($purview[$k], '', false)) {
                                    continue;
                                }
                            }
                        }
                        if ($k == 'ucenter_setup' && $GLOBALS['_CFG']['integrate_code'] != 'ucenter') {
                            continue;
                        }
                        $menus[$key]['children'][$k]['label'] = $GLOBALS['_LANG'][$k];
                        $menus[$key]['children'][$k]['action'] = $v;
                    }
                } else {
                    $menus[$key]['action'] = $val;
                }

                // 濡傛灉children鐨勫瓙鍏冪礌闀垮害涓?鍒欏垹闄よ?缁
                if (empty($menus[$key]['children'])) {
                    unset($menus[$key]);
                }
            }

            $this->smarty->assign('menus', $menus);

            $this->smarty->display('index.htm');
        }

        /**
         * 头部导航
         */
        if ($_REQUEST['act'] == 'top') {
            // 鑾峰緱绠＄悊鍛樿?缃?殑鑿滃崟
            $lst = array();
            $nav = $this->db->GetOne('SELECT nav_list FROM ' . $this->ecs->table('admin_user') . " WHERE user_id = '" . session('admin_id') . "'");

            if (!empty($nav)) {
                $arr = explode(',', $nav);

                foreach ($arr as $val) {
                    $tmp = explode('|', $val);
                    $lst[$tmp[1]] = $tmp[0];
                }
            }

            $this->smarty->assign('send_mail_on', $GLOBALS['_CFG']['send_mail_on']);
            $this->smarty->assign('nav_list', $lst);
            $this->smarty->assign('admin_id', session('admin_id'));
            $this->smarty->assign('certi', $GLOBALS['_CFG']['certi']);

            $this->smarty->display('top.htm');
        }

        /**
         * 计算器
         */
        if ($_REQUEST['act'] == 'calculator') {
            $this->smarty->display('calculator.htm');
        }

        /**
         * 清空缓存
         */
        if ($_REQUEST['act'] == 'clear_cache') {
            clear_all_files();

            sys_msg($GLOBALS['_LANG']['caches_cleared']);
        }

        /**
         * 控制台统计
         */
        if ($_REQUEST['act'] == 'main') {
            //寮€搴楀悜瀵肩?涓€姝
            if (session()->has('shop_guide') && session('shop_guide') === true) {
                session()->forget('shop_guide');

                ecs_header("Location: ./index.php?act=first\n");

                exit();
            }

            $gd = gd_version();

            /* 妫€鏌ユ枃浠剁洰褰曞睘鎬 */
            $warning = array();

            if ($GLOBALS['_CFG']['shop_closed']) {
                $warning[] = $GLOBALS['_LANG']['shop_closed_tips'];
            }

            $open_basedir = ini_get('open_basedir');
            if (!empty($open_basedir)) {
                /* 濡傛灉 open_basedir 涓嶄负绌猴紝鍒欐?鏌ユ槸鍚﹀寘鍚?簡 upload_tmp_dir  */
                $open_basedir = str_replace(array("\\", "\\\\"), array("/", "/"), $open_basedir);
                $upload_tmp_dir = ini_get('upload_tmp_dir');

                if (empty($upload_tmp_dir)) {
                    if (stristr(PHP_OS, 'win')) {
                        $upload_tmp_dir = getenv('TEMP') ? getenv('TEMP') : getenv('TMP');
                        $upload_tmp_dir = str_replace(array("\\", "\\\\"), array("/", "/"), $upload_tmp_dir);
                    } else {
                        $upload_tmp_dir = getenv('TMPDIR') === false ? '/tmp' : getenv('TMPDIR');
                    }
                }

                if (!stristr($open_basedir, $upload_tmp_dir)) {
                    $warning[] = sprintf($GLOBALS['_LANG']['temp_dir_cannt_read'], $upload_tmp_dir);
                }
            }

            $result = file_mode_info(public_path(DATA_DIR));
            if ($result < 2) {
                $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], 'data', $GLOBALS['_LANG']['data_cannt_write']);
            } else {
                $result = file_mode_info(public_path(DATA_DIR . '/afficheimg'));
                if ($result < 2) {
                    $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], DATA_DIR . '/afficheimg', $GLOBALS['_LANG']['afficheimg_cannt_write']);
                }

                $result = file_mode_info(public_path(DATA_DIR . '/brandlogo'));
                if ($result < 2) {
                    $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], DATA_DIR . '/brandlogo', $GLOBALS['_LANG']['brandlogo_cannt_write']);
                }

                $result = file_mode_info(public_path(DATA_DIR . '/cardimg'));
                if ($result < 2) {
                    $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], DATA_DIR . '/cardimg', $GLOBALS['_LANG']['cardimg_cannt_write']);
                }

                $result = file_mode_info(public_path(DATA_DIR . '/feedbackimg'));
                if ($result < 2) {
                    $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], DATA_DIR . '/feedbackimg', $GLOBALS['_LANG']['feedbackimg_cannt_write']);
                }

                $result = file_mode_info(public_path(DATA_DIR . '/packimg'));
                if ($result < 2) {
                    $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], DATA_DIR . '/packimg', $GLOBALS['_LANG']['packimg_cannt_write']);
                }
            }

            $result = file_mode_info(public_path(IMAGE_DIR));
            if ($result < 2) {
                $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], 'images', $GLOBALS['_LANG']['images_cannt_write']);
            } else {
                $result = file_mode_info(public_path(IMAGE_DIR . '/upload'));
                if ($result < 2) {
                    $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], IMAGE_DIR . '/upload', $GLOBALS['_LANG']['imagesupload_cannt_write']);
                }
            }

            $result = file_mode_info(storage_path('temp'));
            if ($result < 2) {
                $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], 'temp', $GLOBALS['_LANG']['tpl_cannt_write']);
            }

            $result = file_mode_info(storage_path('temp/backup'));
            if ($result < 2) {
                $warning[] = sprintf($GLOBALS['_LANG']['not_writable'], 'backup', $GLOBALS['_LANG']['tpl_backup_cannt_write']);
            }

            if (!is_writeable(public_path(DATA_DIR . '/order_print.html'))) {
                $warning[] = $GLOBALS['_LANG']['order_print_canntwrite'];
            }
            clearstatcache();

            $this->smarty->assign('warning_arr', $warning);

            /* 绠＄悊鍛樼暀瑷€淇℃伅 */
            $sql = 'SELECT message_id, sender_id, receiver_id, sent_time, readed, deleted, title, message, user_name ' .
                'FROM ' . $this->ecs->table('admin_message') . ' AS a, ' . $this->ecs->table('admin_user') . ' AS b ' .
                "WHERE a.sender_id = b.user_id AND a.receiver_id = '" . session('admin_id') . "' AND " .
                "a.readed = 0 AND deleted = 0 ORDER BY a.sent_time DESC";
            $admin_msg = $this->db->GetAll($sql);

            $this->smarty->assign('admin_msg', $admin_msg);

            /* 鍙栧緱鏀?寔璐у埌浠樻?鍜屼笉鏀?寔璐у埌浠樻?鐨勬敮浠樻柟寮 */
            $ids = get_pay_ids();

            /* 宸插畬鎴愮殑璁㈠崟 */
            $order['finished'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('order_info') .
                " WHERE 1 " . order_query_sql('finished'));
            $status['finished'] = CS_FINISHED;

            /* 寰呭彂璐х殑璁㈠崟锛 */
            $order['await_ship'] = $this->db->GetOne('SELECT COUNT(*)' .
                ' FROM ' . $this->ecs->table('order_info') .
                " WHERE 1 " . order_query_sql('await_ship'));
            $status['await_ship'] = CS_AWAIT_SHIP;

            /* 寰呬粯娆剧殑璁㈠崟锛 */
            $order['await_pay'] = $this->db->GetOne('SELECT COUNT(*)' .
                ' FROM ' . $this->ecs->table('order_info') .
                " WHERE 1 " . order_query_sql('await_pay'));
            $status['await_pay'] = CS_AWAIT_PAY;

            /* 鈥滄湭纭??鈥濈殑璁㈠崟 */
            $order['unconfirmed'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('order_info') .
                " WHERE 1 " . order_query_sql('unconfirmed'));
            $status['unconfirmed'] = OS_UNCONFIRMED;

            /* 鈥滈儴鍒嗗彂璐р€濈殑璁㈠崟 */
            $order['shipped_part'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('order_info') .
                " WHERE  shipping_status=" . SS_SHIPPED_PART);
            $status['shipped_part'] = OS_SHIPPED_PART;

//    $today_start = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $order['stats'] = $this->db->getRow('SELECT COUNT(*) AS oCount, IFNULL(SUM(order_amount), 0) AS oAmount' .
                ' FROM ' . $this->ecs->table('order_info'));

            $this->smarty->assign('order', $order);
            $this->smarty->assign('status', $status);

            /* 鍟嗗搧淇℃伅 */
            $goods['total'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_alone_sale = 1 AND is_real = 1');
            $virtual_card['total'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_alone_sale = 1 AND is_real=0 AND extension_code=\'virtual_card\'');

            $goods['new'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_new = 1 AND is_real = 1');
            $virtual_card['new'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_new = 1 AND is_real=0 AND extension_code=\'virtual_card\'');

            $goods['best'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_best = 1 AND is_real = 1');
            $virtual_card['best'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_best = 1 AND is_real=0 AND extension_code=\'virtual_card\'');

            $goods['hot'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_hot = 1 AND is_real = 1');
            $virtual_card['hot'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_hot = 1 AND is_real=0 AND extension_code=\'virtual_card\'');

            $time = gmtime();
            $goods['promote'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND promote_price>0' .
                " AND promote_start_date <= '$time' AND promote_end_date >= '$time' AND is_real = 1");
            $virtual_card['promote'] = $this->db->GetOne('SELECT COUNT(*) FROM ' . $this->ecs->table('goods') .
                ' WHERE is_delete = 0 AND promote_price>0' .
                " AND promote_start_date <= '$time' AND promote_end_date >= '$time' AND is_real=0 AND extension_code='virtual_card'");

            /* 缂鸿揣鍟嗗搧 */
            if ($GLOBALS['_CFG']['use_storage']) {
                $sql = 'SELECT COUNT(*) FROM ' . $this->ecs->table('goods') . ' WHERE is_delete = 0 AND goods_number <= warn_number AND is_real = 1';
                $goods['warn'] = $this->db->GetOne($sql);
                $sql = 'SELECT COUNT(*) FROM ' . $this->ecs->table('goods') . ' WHERE is_delete = 0 AND goods_number <= warn_number AND is_real=0 AND extension_code=\'virtual_card\'';
                $virtual_card['warn'] = $this->db->GetOne($sql);
            } else {
                $goods['warn'] = 0;
                $virtual_card['warn'] = 0;
            }
            $this->smarty->assign('goods', $goods);
            $this->smarty->assign('virtual_card', $virtual_card);

            /* 璁块棶缁熻?淇℃伅 */
            $today = local_getdate();
            $sql = 'SELECT COUNT(*) FROM ' . $this->ecs->table('stats') .
                ' WHERE access_time > ' . (mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']) - date('Z'));

            $today_visit = $this->db->GetOne($sql);
            $this->smarty->assign('today_visit', $today_visit);

            $online_users = $this->sess->get_users_count();
            $this->smarty->assign('online_users', $online_users);

            /* 鏈€杩戝弽棣 */
            $sql = "SELECT COUNT(f.msg_id) " .
                "FROM " . $this->ecs->table('feedback') . " AS f " .
                "LEFT JOIN " . $this->ecs->table('feedback') . " AS r ON r.parent_id=f.msg_id " .
                'WHERE f.parent_id=0 AND ISNULL(r.msg_id) ';
            $this->smarty->assign('feedback_number', $this->db->GetOne($sql));

            /* 鏈??鏍歌瘎璁 */
            $this->smarty->assign('comment_number', $this->db->getOne('SELECT COUNT(*) FROM ' . $this->ecs->table('comment') .
                ' WHERE status = 0 AND parent_id = 0'));

            $mysql_ver = $this->db->version();   // 鑾峰緱 MySQL 鐗堟湰

            /* 绯荤粺淇℃伅 */
            $sys_info['os'] = PHP_OS;
            $sys_info['ip'] = $_SERVER['SERVER_ADDR'];
            $sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
            $sys_info['php_ver'] = PHP_VERSION;
            $sys_info['mysql_ver'] = $mysql_ver;
            $sys_info['zlib'] = function_exists('gzclose') ? $GLOBALS['_LANG']['yes'] : $GLOBALS['_LANG']['no'];
            $sys_info['safe_mode'] = (boolean)ini_get('safe_mode') ? $GLOBALS['_LANG']['yes'] : $GLOBALS['_LANG']['no'];
            $sys_info['safe_mode_gid'] = (boolean)ini_get('safe_mode_gid') ? $GLOBALS['_LANG']['yes'] : $GLOBALS['_LANG']['no'];
            $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : $GLOBALS['_LANG']['no_timezone'];
            $sys_info['socket'] = function_exists('fsockopen') ? $GLOBALS['_LANG']['yes'] : $GLOBALS['_LANG']['no'];

            if ($gd == 0) {
                $sys_info['gd'] = 'N/A';
            } else {
                if ($gd == 1) {
                    $sys_info['gd'] = 'GD1';
                } else {
                    $sys_info['gd'] = 'GD2';
                }

                $sys_info['gd'] .= ' (';

                /* 妫€鏌ョ郴缁熸敮鎸佺殑鍥剧墖绫诲瀷 */
                if ($gd && (imagetypes() & IMG_JPG) > 0) {
                    $sys_info['gd'] .= ' JPEG';
                }

                if ($gd && (imagetypes() & IMG_GIF) > 0) {
                    $sys_info['gd'] .= ' GIF';
                }

                if ($gd && (imagetypes() & IMG_PNG) > 0) {
                    $sys_info['gd'] .= ' PNG';
                }

                $sys_info['gd'] .= ')';
            }

            /* IP搴撶増鏈 */
            $sys_info['ip_version'] = ecs_geoip('255.255.255.0');

            /* 鍏佽?涓婁紶鐨勬渶澶ф枃浠跺ぇ灏 */
            $sys_info['max_filesize'] = ini_get('upload_max_filesize');

            $this->smarty->assign('sys_info', $sys_info);

            /* 缂鸿揣鐧昏? */
            $this->smarty->assign('booking_goods', $this->db->getOne('SELECT COUNT(*) FROM ' . $this->ecs->table('booking_goods') . ' WHERE is_dispose = 0'));

            /* 閫€娆剧敵璇 */
            $this->smarty->assign('new_repay', $this->db->getOne('SELECT COUNT(*) FROM ' . $this->ecs->table('user_account') . ' WHERE process_type = ' . SURPLUS_RETURN . ' AND is_paid = 0 '));


            $this->smarty->assign('ecs_version', VERSION);
            $this->smarty->assign('ecs_release', RELEASE);
            $this->smarty->assign('ecs_lang', $GLOBALS['_CFG']['lang']);
            $this->smarty->assign('ecs_charset', strtoupper(CHARSET));
            $this->smarty->assign('install_date', local_date($GLOBALS['_CFG']['date_format'], $GLOBALS['_CFG']['install_date']));
            $this->smarty->display('start.htm');
        }

        /**
         * 开店向导1
         */
        if ($_REQUEST['act'] == 'first') {
            $this->smarty->assign('countries', get_regions());
            $this->smarty->assign('provinces', get_regions(1, 1));
            $this->smarty->assign('cities', get_regions(2, 2));

            $sql = 'SELECT value from ' . $this->ecs->table('shop_config') . " WHERE code='shop_name'";
            $shop_name = $this->db->getOne($sql);

            $this->smarty->assign('shop_name', $shop_name);

            $sql = 'SELECT value from ' . $this->ecs->table('shop_config') . " WHERE code='shop_title'";
            $shop_title = $this->db->getOne($sql);

            $this->smarty->assign('shop_title', $shop_title);

            //鑾峰彇閰嶉€佹柟寮
//    $modules = read_modules('../includes/modules/shipping');
            $directory = ROOT_PATH . 'includes/modules/shipping';
            $dir = @opendir($directory);
            $set_modules = true;
            $modules = array();

            while (false !== ($file = @readdir($dir))) {
                if (preg_match("/^.*?\.php$/", $file)) {
                    if ($file != 'express.php') {
                        include_once($directory . '/' . $file);
                    }
                }
            }
            @closedir($dir);
            unset($set_modules);

            foreach ($modules as $key => $value) {
                ksort($modules[$key]);
            }
            ksort($modules);

            for ($i = 0; $i < count($modules); $i++) {
                $lang_file = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/shipping/' . $modules[$i]['code'] . '.php';

                if (file_exists($lang_file)) {
                    include_once($lang_file);
                }

                $modules[$i]['name'] = $GLOBALS['_LANG'][$modules[$i]['code']];
                $modules[$i]['desc'] = $GLOBALS['_LANG'][$modules[$i]['desc']];
                $modules[$i]['insure_fee'] = empty($modules[$i]['insure']) ? 0 : $modules[$i]['insure'];
                $modules[$i]['cod'] = $modules[$i]['cod'];
                $modules[$i]['install'] = 0;
            }
            $this->smarty->assign('modules', $modules);

            unset($modules);

            //鑾峰彇鏀?粯鏂瑰紡
            $modules = read_modules('../includes/modules/payment');

            for ($i = 0; $i < count($modules); $i++) {
                $code = $modules[$i]['code'];
                $modules[$i]['name'] = $GLOBALS['_LANG'][$modules[$i]['code']];
                if (!isset($modules[$i]['pay_fee'])) {
                    $modules[$i]['pay_fee'] = 0;
                }
                $modules[$i]['desc'] = $GLOBALS['_LANG'][$modules[$i]['desc']];
            }
            //        $modules[$i]['install'] = '0';
            $this->smarty->assign('modules_payment', $modules);


            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['ur_config']);
            $this->smarty->display('setting_first.htm');
        }

        /**
         * 开店向导2
         */
        if ($_REQUEST['act'] == 'second') {
            admin_priv('shop_config');

            $shop_name = empty($_POST['shop_name']) ? '' : $_POST['shop_name'];
            $shop_title = empty($_POST['shop_title']) ? '' : $_POST['shop_title'];
            $shop_country = empty($_POST['shop_country']) ? '' : intval($_POST['shop_country']);
            $shop_province = empty($_POST['shop_province']) ? '' : intval($_POST['shop_province']);
            $shop_city = empty($_POST['shop_city']) ? '' : intval($_POST['shop_city']);
            $shop_address = empty($_POST['shop_address']) ? '' : $_POST['shop_address'];
            $shipping = empty($_POST['shipping']) ? '' : $_POST['shipping'];
            $payment = empty($_POST['payment']) ? '' : preg_replace('/[\'|\/|\\\]/', '', $_POST['payment']);

            if (!empty($shop_name)) {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . " SET value = '$shop_name' WHERE code = 'shop_name'";
                $this->db->query($sql);
            }

            if (!empty($shop_title)) {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . " SET value = '$shop_title' WHERE code = 'shop_title'";
                $this->db->query($sql);
            }

            if (!empty($shop_address)) {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . " SET value = '$shop_address' WHERE code = 'shop_address'";
                $this->db->query($sql);
            }

            if (!empty($shop_country)) {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . "SET value = '$shop_country' WHERE code='shop_country'";
                $this->db->query($sql);
            }

            if (!empty($shop_province)) {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . "SET value = '$shop_province' WHERE code='shop_province'";
                $this->db->query($sql);
            }

            if (!empty($shop_city)) {
                $sql = 'UPDATE ' . $this->ecs->table('shop_config') . "SET value = '$shop_city' WHERE code='shop_city'";
                $this->db->query($sql);
            }

            //璁剧疆閰嶉€佹柟寮
            if (!empty($shipping)) {
                $shop_add = read_modules('../includes/modules/shipping');

                foreach ($shop_add as $val) {
                    $mod_shop[] = $val['code'];
                }
                $mod_shop = implode(',', $mod_shop);

                $set_modules = true;
                if (strpos($mod_shop, $shipping) === false) {
                    exit;
                } else {
                    include_once(ROOT_PATH . 'includes/modules/shipping/' . $shipping . '.php');
                }
                $sql = "SELECT shipping_id FROM " . $this->ecs->table('shipping') . " WHERE shipping_code = '$shipping'";
                $shipping_id = $this->db->GetOne($sql);

                if ($shipping_id <= 0) {
                    $insure = empty($modules[0]['insure']) ? 0 : $modules[0]['insure'];
                    $sql = "INSERT INTO " . $this->ecs->table('shipping') . " (" .
                        "shipping_code, shipping_name, shipping_desc, insure, support_cod, enabled" .
                        ") VALUES (" .
                        "'" . addslashes($modules[0]['code']) . "', '" . addslashes($GLOBALS['_LANG'][$modules[0]['code']]) . "', '" .
                        addslashes($GLOBALS['_LANG'][$modules[0]['desc']]) . "', '$insure', '" . intval($modules[0]['cod']) . "', 1)";
                    $this->db->query($sql);
                    $shipping_id = $this->db->insert_Id();
                }

                //璁剧疆閰嶉€佸尯鍩
                $area_name = empty($_POST['area_name']) ? '' : $_POST['area_name'];
                if (!empty($area_name)) {
                    $sql = "SELECT shipping_area_id FROM " . $this->ecs->table("shipping_area") .
                        " WHERE shipping_id='$shipping_id' AND shipping_area_name='$area_name'";
                    $area_id = $this->db->getOne($sql);

                    if ($area_id <= 0) {
                        $config = array();
                        foreach ($modules[0]['configure'] as $key => $val) {
                            $config[$key]['name'] = $val['name'];
                            $config[$key]['value'] = $val['value'];
                        }

                        $count = count($config);
                        $config[$count]['name'] = 'free_money';
                        $config[$count]['value'] = 0;

                        /* 濡傛灉鏀?寔璐у埌浠樻?锛屽垯鍏佽?璁剧疆璐у埌浠樻?鏀?粯璐圭敤 */
                        if ($modules[0]['cod']) {
                            $count++;
                            $config[$count]['name'] = 'pay_fee';
                            $config[$count]['value'] = make_semiangle(0);
                        }

                        $sql = "INSERT INTO " . $this->ecs->table('shipping_area') .
                            " (shipping_area_name, shipping_id, configure) " .
                            "VALUES" . " ('$area_name', '$shipping_id', '" . serialize($config) . "')";
                        $this->db->query($sql);
                        $area_id = $this->db->insert_Id();
                    }

                    $region_id = empty($_POST['shipping_country']) ? 1 : intval($_POST['shipping_country']);
                    $region_id = empty($_POST['shipping_province']) ? $region_id : intval($_POST['shipping_province']);
                    $region_id = empty($_POST['shipping_city']) ? $region_id : intval($_POST['shipping_city']);
                    $region_id = empty($_POST['shipping_district']) ? $region_id : intval($_POST['shipping_district']);

                    /* 娣诲姞閫夊畾鐨勫煄甯傚拰鍦板尯 */
                    $sql = "REPLACE INTO " . $this->ecs->table('area_region') . " (shipping_area_id, region_id) VALUES ('$area_id', '$region_id')";
                    $this->db->query($sql);
                }
            }

            unset($modules);

            if (!empty($payment)) {
                /* 鍙栫浉搴旀彃浠朵俊鎭 */
                $set_modules = true;
                include_once(ROOT_PATH . 'includes/modules/payment/' . $payment . '.php');

                $pay_config = array();
                if (isset($_REQUEST['cfg_value']) && is_array($_REQUEST['cfg_value'])) {
                    for ($i = 0; $i < count($_POST['cfg_value']); $i++) {
                        $pay_config[] = array('name' => trim($_POST['cfg_name'][$i]),
                            'type' => trim($_POST['cfg_type'][$i]),
                            'value' => trim($_POST['cfg_value'][$i])
                        );
                    }
                }

                $pay_config = serialize($pay_config);
                /* 瀹夎?锛屾?鏌ヨ?鏀?粯鏂瑰紡鏄?惁鏇剧粡瀹夎?杩 */
                $sql = "SELECT COUNT(*) FROM " . $this->ecs->table('payment') . " WHERE pay_code = '$payment'";
                if ($this->db->GetOne($sql) > 0) {
                    $sql = "UPDATE " . $this->ecs->table('payment') .
                        " SET pay_config = '$pay_config'," .
                        " enabled = '1' " .
                        "WHERE pay_code = '$payment' LIMIT 1";
                    $this->db->query($sql);
                } else {
                    //            $modules = read_modules('../includes/modules/payment');

                    $payment_info = array();
                    $payment_info['name'] = $GLOBALS['_LANG'][$modules[0]['code']];
                    $payment_info['pay_fee'] = empty($modules[0]['pay_fee']) ? 0 : $modules[0]['pay_fee'];
                    $payment_info['desc'] = $GLOBALS['_LANG'][$modules[0]['desc']];

                    $sql = "INSERT INTO " . $this->ecs->table('payment') . " (pay_code, pay_name, pay_desc, pay_config, is_cod, pay_fee, enabled, is_online)" .
                        "VALUES ('$payment', '$payment_info[name]', '$payment_info[desc]', '$pay_config', '0', '$payment_info[pay_fee]', '1', '1')";
                    $this->db->query($sql);
                }
            }

            clear_all_files();


            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['ur_add']);
            $this->smarty->display('setting_second.htm');
        }

        /**
         * 开店向导3
         */
        if ($_REQUEST['act'] == 'third') {
            admin_priv('goods_manage');

            $good_name = empty($_POST['good_name']) ? '' : $_POST['good_name'];
            $good_number = empty($_POST['good_number']) ? '' : $_POST['good_number'];
            $good_category = empty($_POST['good_category']) ? '' : $_POST['good_category'];
            $good_brand = empty($_POST['good_brand']) ? '' : $_POST['good_brand'];
            $good_price = empty($_POST['good_price']) ? 0 : $_POST['good_price'];
            $good_name = empty($_POST['good_name']) ? '' : $_POST['good_name'];
            $is_best = empty($_POST['is_best']) ? 0 : 1;
            $is_new = empty($_POST['is_new']) ? 0 : 1;
            $is_hot = empty($_POST['is_hot']) ? 0 : 1;
            $good_brief = empty($_POST['good_brief']) ? '' : $_POST['good_brief'];
            $market_price = $good_price * 1.2;

            if (!empty($good_category)) {
                if (cat_exists($good_category, 0)) {
                    /* 鍚岀骇鍒?笅涓嶈兘鏈夐噸澶嶇殑鍒嗙被鍚嶇О */
                    $link[] = array('text' => $GLOBALS['_LANG']['go_back'], 'href' => 'javascript:history.back(-1)');
                    sys_msg($GLOBALS['_LANG']['catname_exist'], 0, $link);
                }
            }

            if (!empty($good_brand)) {
                if (brand_exists($good_brand)) {
                    /* 鍚岀骇鍒?笅涓嶈兘鏈夐噸澶嶇殑鍝佺墝鍚嶇О */
                    $link[] = array('text' => $GLOBALS['_LANG']['go_back'], 'href' => 'javascript:history.back(-1)');
                    sys_msg($GLOBALS['_LANG']['brand_name_exist'], 0, $link);
                }
            }

            $brand_id = 0;
            if (!empty($good_brand)) {
                $sql = 'INSERT INTO ' . $this->ecs->table('brand') . " (brand_name, is_show)" .
                    " values('" . $good_brand . "', '1')";
                $this->db->query($sql);

                $brand_id = $this->db->insert_Id();
            }

            if (!empty($good_category)) {
                $sql = 'INSERT INTO ' . $this->ecs->table('category') . " (cat_name, parent_id, is_show)" .
                    " values('" . $good_category . "', '0', '1')";
                $this->db->query($sql);

                $cat_id = $this->db->insert_Id();

                //璐у彿
                require_once(ROOT_PATH . ADMIN_PATH . '/includes/lib_goods.php');
                $max_id = $this->db->getOne("SELECT MAX(goods_id) + 1 FROM " . $this->ecs->table('goods'));
                $goods_sn = generate_goods_sn($max_id);

                // include_once(ROOT_PATH . 'includes/cls_image.php');
                $image = new cls_image($GLOBALS['_CFG']['bgcolor']);

                if (!empty($good_name)) {
                    /* 妫€鏌ュ浘鐗囷細濡傛灉鏈夐敊璇?紝妫€鏌ュ昂瀵告槸鍚﹁秴杩囨渶澶у€硷紱鍚﹀垯锛屾?鏌ユ枃浠剁被鍨 */
                    if (isset($_FILES['goods_img']['error'])) { // php 4.2 鐗堟湰鎵嶆敮鎸 error
                        // 鏈€澶т笂浼犳枃浠跺ぇ灏
                        $php_maxsize = ini_get('upload_max_filesize');
                        $htm_maxsize = '2M';

                        // 鍟嗗搧鍥剧墖
                        if ($_FILES['goods_img']['error'] == 0) {
                            if (!$image->check_img_type($_FILES['goods_img']['type'])) {
                                sys_msg($GLOBALS['_LANG']['invalid_goods_img'], 1, array(), false);
                            }
                        } elseif ($_FILES['goods_img']['error'] == 1) {
                            sys_msg(sprintf($GLOBALS['_LANG']['goods_img_too_big'], $php_maxsize), 1, array(), false);
                        } elseif ($_FILES['goods_img']['error'] == 2) {
                            sys_msg(sprintf($GLOBALS['_LANG']['goods_img_too_big'], $htm_maxsize), 1, array(), false);
                        }
                    } /* 4銆?鐗堟湰 */
                    else {
                        // 鍟嗗搧鍥剧墖
                        if ($_FILES['goods_img']['tmp_name'] != 'none') {
                            if (!$image->check_img_type($_FILES['goods_img']['type'])) {
                                sys_msg($GLOBALS['_LANG']['invalid_goods_img'], 1, array(), false);
                            }
                        }
                    }
                    $goods_img = '';  // 鍒濆?鍖栧晢鍝佸浘鐗
                    $goods_thumb = '';  // 鍒濆?鍖栧晢鍝佺缉鐣ュ浘
                    $original_img = '';  // 鍒濆?鍖栧師濮嬪浘鐗
                    $old_original_img = '';  // 鍒濆?鍖栧師濮嬪浘鐗囨棫鍥
                    // 濡傛灉涓婁紶浜嗗晢鍝佸浘鐗囷紝鐩稿簲澶勭悊
                    if ($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none') {
                        $original_img = $image->upload_image($_FILES['goods_img']); // 鍘熷?鍥剧墖
                        if ($original_img === false) {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                        $goods_img = $original_img;   // 鍟嗗搧鍥剧墖

                        /* 澶嶅埗涓€浠界浉鍐屽浘鐗 */
                        $img = $original_img;   // 鐩稿唽鍥剧墖
                        $pos = strpos(basename($img), '.');
                        $newname = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                        if (!copy('../' . $img, '../' . $newname)) {
                            sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                        }
                        $img = $newname;

                        $gallery_img = $img;
                        $gallery_thumb = $img;

                        // 濡傛灉绯荤粺鏀?寔GD锛岀缉鏀惧晢鍝佸浘鐗囷紝涓旂粰鍟嗗搧鍥剧墖鍜岀浉鍐屽浘鐗囧姞姘村嵃
                        if ($image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type'])) {
                            // 濡傛灉璁剧疆澶у皬涓嶄负0锛岀缉鏀惧浘鐗
                            if ($GLOBALS['_CFG']['image_width'] != 0 || $GLOBALS['_CFG']['image_height'] != 0) {
                                $goods_img = $image->make_thumb('../' . $goods_img, $GLOBALS['_CFG']['image_width'], $GLOBALS['_CFG']['image_height']);
                                if ($goods_img === false) {
                                    sys_msg($image->error_msg(), 1, array(), false);
                                }
                            }

                            $newname = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                            if (!copy('../' . $img, '../' . $newname)) {
                                sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                            }
                            $gallery_img = $newname;

                            // 鍔犳按鍗
                            if (intval($GLOBALS['_CFG']['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark'])) {
                                if ($image->add_watermark('../' . $goods_img, '', $GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false) {
                                    sys_msg($image->error_msg(), 1, array(), false);
                                }

                                if ($image->add_watermark('../' . $gallery_img, '', $GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false) {
                                    sys_msg($image->error_msg(), 1, array(), false);
                                }
                            }

                            // 鐩稿唽缂╃暐鍥
                            if ($GLOBALS['_CFG']['thumb_width'] != 0 || $GLOBALS['_CFG']['thumb_height'] != 0) {
                                $gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'], $GLOBALS['_CFG']['thumb_height']);
                                if ($gallery_thumb === false) {
                                    sys_msg($image->error_msg(), 1, array(), false);
                                }
                            }
                        } else {
                            /* 澶嶅埗涓€浠藉師鍥 */
                            $pos = strpos(basename($img), '.');
                            $gallery_img = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                            if (!copy('../' . $img, '../' . $gallery_img)) {
                                sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                            }
                            $gallery_thumb = '';
                        }
                    }
                    // 鏈?笂浼狅紝濡傛灉鑷?姩閫夋嫨鐢熸垚锛屼笖涓婁紶浜嗗晢鍝佸浘鐗囷紝鐢熸垚鎵€鐣ュ浘
                    if (!empty($original_img)) {
                        // 濡傛灉璁剧疆缂╃暐鍥惧ぇ灏忎笉涓?锛岀敓鎴愮缉鐣ュ浘
                        if ($GLOBALS['_CFG']['thumb_width'] != 0 || $GLOBALS['_CFG']['thumb_height'] != 0) {
                            $goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'], $GLOBALS['_CFG']['thumb_height']);
                            if ($goods_thumb === false) {
                                sys_msg($image->error_msg(), 1, array(), false);
                            }
                        } else {
                            $goods_thumb = $original_img;
                        }
                    }


                    $sql = 'INSERT INTO ' . $this->ecs->table('goods') . "(goods_name, goods_sn, goods_number, cat_id, brand_id, goods_brief, shop_price, market_price, goods_img, goods_thumb, original_img,add_time, last_update,
                   is_best, is_new, is_hot)" .
                        "VALUES('$good_name', '$goods_sn', '$good_number', '$cat_id', '$brand_id', '$good_brief', '$good_price'," .
                        " '$market_price', '$goods_img', '$goods_thumb', '$original_img','" . gmtime() . "', '" . gmtime() . "', '$is_best', '$is_new', '$is_hot')";

                    $this->db->query($sql);
                    $good_id = $this->db->insert_id();
                    /* 濡傛灉鏈夊浘鐗囷紝鎶婂晢鍝佸浘鐗囧姞鍏ュ浘鐗囩浉鍐 */
                    if (isset($img)) {
                        $sql = "INSERT INTO " . $this->ecs->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
                            "VALUES ('$good_id', '$gallery_img', '', '$gallery_thumb', '$img')";
                        $this->db->query($sql);
                    }
                }
            }


            //    $this->smarty->assign('ur_here', '寮€搴楀悜瀵硷紞娣诲姞鍟嗗搧');
            $this->smarty->display('setting_third.htm');
        }

        /**
         * 关于我们
         */
        if ($_REQUEST['act'] == 'about_us') {
            $this->smarty->display('about_us.htm');
        }

        /**
         * 拖拽工具栏
         */
        if ($_REQUEST['act'] == 'drag') {
            $this->smarty->display('drag.htm');;
        }

        /**
         *
         * 订单检测
         */
        if ($_REQUEST['act'] == 'check_order') {
            if (empty(session('last_check'))) {
                session(['last_check' => gmtime()]);

                make_json_result('', '', array('new_orders' => 0, 'new_paid' => 0));
            }

            /* 鏂拌?鍗 */
            $sql = 'SELECT COUNT(*) FROM ' . $this->ecs->table('order_info') .
                " WHERE add_time >= '" . session('last_check') . "'";
            $arr['new_orders'] = $this->db->getOne($sql);

            /* 鏂颁粯娆剧殑璁㈠崟 */
            $sql = 'SELECT COUNT(*) FROM ' . $this->ecs->table('order_info') .
                ' WHERE pay_time >= ' . session('last_check');
            $arr['new_paid'] = $this->db->getOne($sql);

            session(['last_check' => gmtime()]);

            if (!(is_numeric($arr['new_orders']) && is_numeric($arr['new_paid']))) {
                make_json_error($this->db->error());
            } else {
                make_json_result('', '', $arr);
            }
        }

        /**
         * 保存待办事项
         */
        if ($_REQUEST['act'] == 'save_todolist') {
            $content = json_str_iconv($_POST["content"]);
            $sql = "UPDATE" . $GLOBALS['ecs']->table('admin_user') . " SET todolist='" . $content . "' WHERE user_id = " . session('admin_id');
            $GLOBALS['db']->query($sql);
        }

        /**
         * 获取待办事项
         */
        if ($_REQUEST['act'] == 'get_todolist') {
            $sql = "SELECT todolist FROM " . $GLOBALS['ecs']->table('admin_user') . " WHERE user_id = " . session('admin_id');
            $content = $GLOBALS['db']->getOne($sql);
            echo $content;
        }

        /**
         * 邮件发送
         */
        if ($_REQUEST['act'] == 'send_mail') {
            if ($GLOBALS['_CFG']['send_mail_on'] == 'off') {
                make_json_result('', $GLOBALS['_LANG']['send_mail_off'], 0);
                exit();
            }
            $sql = "SELECT * FROM " . $this->ecs->table('email_sendlist') . " ORDER BY pri DESC, last_send ASC LIMIT 1";
            $row = $this->db->getRow($sql);

            //鍙戦€佸垪琛ㄤ负绌
            if (empty($row['id'])) {
                make_json_result('', $GLOBALS['_LANG']['mailsend_null'], 0);
            }

            //鍙戦€佸垪琛ㄤ笉涓虹┖锛岄偖浠跺湴鍧€涓虹┖
            if (!empty($row['id']) && empty($row['email'])) {
                $sql = "DELETE FROM " . $this->ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                $this->db->query($sql);
                $count = $this->db->getOne("SELECT COUNT(*) FROM " . $this->ecs->table('email_sendlist'));
                make_json_result('', $GLOBALS['_LANG']['mailsend_skip'], array('count' => $count, 'goon' => 1));
            }

            //鏌ヨ?鐩稿叧妯℃澘
            $sql = "SELECT * FROM " . $this->ecs->table('mail_templates') . " WHERE template_id = '$row[template_id]'";
            $rt = $this->db->getRow($sql);

            //濡傛灉鏄?ā鏉匡紝鍒欏皢宸插瓨鍏?mail_sendlist鐨勫唴瀹逛綔涓洪偖浠跺唴瀹
            //鍚﹀垯鍗虫槸鏉傝川锛屽皢mail_templates璋冨嚭鐨勫唴瀹逛綔涓洪偖浠跺唴瀹
            if ($rt['type'] == 'template') {
                $rt['template_content'] = $row['email_content'];
            }

            if ($rt['template_id'] && $rt['template_content']) {
                if (send_mail('', $row['email'], $rt['template_subject'], $rt['template_content'], $rt['is_html'])) {
                    //鍙戦€佹垚鍔

                    //浠庡垪琛ㄤ腑鍒犻櫎
                    $sql = "DELETE FROM " . $this->ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                    $this->db->query($sql);

                    //鍓╀綑鍒楄〃鏁
                    $count = $this->db->getOne("SELECT COUNT(*) FROM " . $this->ecs->table('email_sendlist'));

                    if ($count > 0) {
                        $msg = sprintf($GLOBALS['_LANG']['mailsend_ok'], $row['email'], $count);
                    } else {
                        $msg = sprintf($GLOBALS['_LANG']['mailsend_finished'], $row['email']);
                    }
                    make_json_result('', $msg, array('count' => $count));
                } else {
                    //鍙戦€佸嚭閿

                    if ($row['error'] < 3) {
                        $time = time();
                        $sql = "UPDATE " . $this->ecs->table('email_sendlist') . " SET error = error + 1, pri = 0, last_send = '$time' WHERE id = '$row[id]'";
                    } else {
                        //灏嗗嚭閿欒秴娆＄殑绾?綍鍒犻櫎
                        $sql = "DELETE FROM " . $this->ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                    }
                    $this->db->query($sql);

                    $count = $this->db->getOne("SELECT COUNT(*) FROM " . $this->ecs->table('email_sendlist'));
                    make_json_result('', sprintf($GLOBALS['_LANG']['mailsend_fail'], $row['email']), array('count' => $count));
                }
            } else {
                //鏃犳晥鐨勯偖浠堕槦鍒
                $sql = "DELETE FROM " . $this->ecs->table('email_sendlist') . " WHERE id = '$row[id]'";
                $this->db->query($sql);
                $count = $this->db->getOne("SELECT COUNT(*) FROM " . $this->ecs->table('email_sendlist'));
                make_json_result('', sprintf($GLOBALS['_LANG']['mailsend_fail'], $row['email']), array('count' => $count));
            }
        }

        /**
         * license
         */
        if ($_REQUEST['act'] == 'license') {
            $is_ajax = $_GET['is_ajax'];

            if (isset($is_ajax) && $is_ajax) {
                // license 妫€鏌
                load_helper(['main', 'license']);

                $license = $this->license_check();
                switch ($license['flag']) {
                    case 'login_succ':
                        if (isset($license['request']['info']['service']['ecshop_b2c']['cert_auth']['auth_str'])) {
                            make_json_result(process_login_license($license['request']['info']['service']['ecshop_b2c']['cert_auth']));
                        } else {
                            make_json_error(0);
                        }
                        break;

                    case 'login_fail':
                    case 'login_ping_fail':
                        make_json_error(0);
                        break;

                    case 'reg_succ':
                        $_license = $this->license_check();
                        switch ($_license['flag']) {
                            case 'login_succ':
                                if (isset($_license['request']['info']['service']['ecshop_b2c']['cert_auth']['auth_str']) && $_license['request']['info']['service']['ecshop_b2c']['cert_auth']['auth_str'] != '') {
                                    make_json_result(process_login_license($license['request']['info']['service']['ecshop_b2c']['cert_auth']));
                                } else {
                                    make_json_error(0);
                                }
                                break;

                            case 'login_fail':
                            case 'login_ping_fail':
                                make_json_error(0);
                                break;
                        }
                        break;

                    case 'reg_fail':
                    case 'reg_ping_fail':
                        make_json_error(0);
                        break;
                }
            } else {
                make_json_error(0);
            }
        }
    }

    /**
     * license check
     * @return  bool
     */
    private function license_check()
    {
        // return 杩斿洖鏁扮粍
        $return_array = array();

        // 鍙栧嚭缃戝簵 license
        $license = get_shop_license();

        // 妫€娴嬬綉搴 license
        if (!empty($license['certificate_id']) && !empty($license['token']) && !empty($license['certi'])) {
            // license锛堢櫥褰曪級
            $return_array = license_login();
        } else {
            // license锛堟敞鍐岋級
            $return_array = license_reg();
        }

        return $return_array;
    }
}
