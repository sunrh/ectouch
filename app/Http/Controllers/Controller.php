<?php

namespace App\Http\Controllers;

use App\Libraries\Error;
use App\Libraries\Mysql;
use App\Libraries\session;
use App\Libraries\shop;
use App\Libraries\Template;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $ecs;
    protected $db;
    protected $err;
    protected $sess;
    protected $smarty;
    protected $_CFG;
    protected $user;

    public function __construct(Request $request)
    {
        define('PHP_SELF', basename(substr(basename($_SERVER['REQUEST_URI']), 0, stripos(basename($_SERVER['REQUEST_URI']), '?')), '.php'));

        $_GET = $request->query() + $request->route()->parameters();
        $_POST = $request->post();
        $_REQUEST = $_GET + $_POST;
        $_REQUEST['act'] = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

        load_helper(['time', 'base', 'common', 'main', 'insert', 'goods', 'article']);

        $this->ecs = $GLOBALS['ecs'] = new Shop();
        define('DATA_DIR', $this->ecs->data_dir());
        define('IMAGE_DIR', $this->ecs->image_dir());

        /* 初始化数据库类 */
        $this->db = $GLOBALS['db'] = new Mysql();

        /* 创建错误处理对象 */
        $this->err = $GLOBALS['err'] = new Error('message.dwt');

        /* 载入系统参数 */
        $this->_CFG = $GLOBALS['_CFG'] = load_config();

        /* 载入语言文件 */
        load_lang('common');

        if ($GLOBALS['_CFG']['shop_closed'] == 1) {
            /* 商店关闭了，输出关闭的消息 */
            header('Content-type: text/html; charset=' . CHARSET);
            die('<div style="margin: 150px; text-align: center; font-size: 14px"><p>' . $GLOBALS['_LANG']['shop_closed'] . '</p><p>' . $GLOBALS['_CFG']['close_comment'] . '</p></div>');
        }

        if (is_spider()) {
            /* 如果是蜘蛛的访问，那么默认为访客方式，并且不记录到日志中 */
            if (!defined('INIT_NO_USERS')) {
                define('INIT_NO_USERS', true);
                /* 整合UC后，如果是蜘蛛访问，初始化UC需要的常量 */
                if ($GLOBALS['_CFG']['integrate_code'] == 'ucenter') {
                    $this->user = $GLOBALS['user'] = &init_users();
                }
            }
            session()->flush();
            session(['user_id' => 0]);
            session(['user_name' => '']);
            session(['email' => '']);
            session(['user_rank' => 0]);
            session(['discount' => 1.00]);
        }

        if (!defined('INIT_NO_USERS')) {
            define('SESS_ID', session()->getId());
        }

        if (isset($_SERVER['PHP_SELF'])) {
            $_SERVER['PHP_SELF'] = htmlspecialchars($_SERVER['PHP_SELF']);
        }

        if (!defined('INIT_NO_SMARTY')) {
            header('Cache-control: private');
            header('Content-type: text/html; charset=' . CHARSET);

            $app_run_mode = config('app.app_run_mode');
            if (($app_run_mode == 0 && is_mobile_device()) || $app_run_mode == 2) {
                $GLOBALS['_CFG']['template'] .= '/mobile';
            }

            $this->smarty = $GLOBALS['smarty'] = new Template();
            $this->smarty->cache_lifetime = $GLOBALS['_CFG']['cache_time'];
            $this->smarty->template_dir = resource_path('themes/' . $GLOBALS['_CFG']['template']);
            $this->smarty->cache_dir = storage_path('temp/caches');
            $this->smarty->compile_dir = storage_path('temp/compiled');

            if (config('app.debug')) {
                $this->smarty->direct_output = true;
                $this->smarty->force_compile = true;
            } else {
                $this->smarty->direct_output = false;
                $this->smarty->force_compile = false;
            }

            $this->smarty->assign('lang', $GLOBALS['_LANG']);
            $this->smarty->assign('ecs_charset', CHARSET);
            if (!empty($GLOBALS['_CFG']['stylename'])) {
                $ecs_css_path = asset('themes/' . $GLOBALS['_CFG']['template'] . '/style_' . $GLOBALS['_CFG']['stylename'] . '.css');
            } else {
                $ecs_css_path = asset('themes/' . $GLOBALS['_CFG']['template'] . '/style.css');
            }
            $this->smarty->assign('ecs_css_path', $ecs_css_path);
        }

        if (!defined('INIT_NO_USERS')) {
            /* 会员信息 */
            $this->user = $GLOBALS['user'] =& init_users();

            if (!session()->has('user_id')) {
                /* 获取投放站点的名称 */
                $site_name = isset($_GET['from']) ? htmlspecialchars($_GET['from']) : addslashes($GLOBALS['_LANG']['self_site']);
                $from_ad = !empty($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

                session(['from_ad' => $from_ad]); // 用户点击的广告ID
                session(['referer' => stripslashes($site_name)]); // 用户来源

                unset($site_name);

                if (!defined('INGORE_VISIT_STATS')) {
                    visit_stats();
                }
            }

            if (empty(session('user_id'))) {
                if ($this->user->get_cookie()) {
                    /* 如果会员已经登录并且还没有获得会员的帐户余额、积分以及优惠券 */
                    if (session('user_id') > 0) {
                        update_user_info();
                    }
                } else {
                    session(['user_id' => 0]);
                    session(['user_name' => '']);
                    session(['email' => '']);
                    session(['user_rank' => 0]);
                    session(['discount' => 1.00]);
                    if (!session()->has('login_fail')) {
                        session(['login_fail' => 0]);
                    }
                }
            }

            /**
             * 设置推荐会员
             */
            if (isset($_GET['u'])) {
                set_affiliate();
            }

            /**
             * session 不存在，检查cookie
             */
            if (!empty(request()->cookie('ectouch_user_id')) && !empty(request()->cookie('ectouch_password'))) {
                // 找到了cookie, 验证cookie信息
                $sql = 'SELECT user_id, user_name, password ' .
                    ' FROM ' . $this->ecs->table('users') .
                    " WHERE user_id = '" . intval(request()->cookie('ectouch_user_id')) . "' AND password = '" . request()->cookie('ectouch_password') . "'";

                $row = $this->db->GetRow($sql);

                if (!$row) {
                    // 没有找到这个记录
                    $time = 0;
                    cookie()->queue('ectouch_user_id', '', $time);
                    cookie()->queue('ectouch_password', '', $time);
                } else {
                    session(['user_id' => $row['user_id']]);
                    session(['user_name' => $row['user_name']]);
                    update_user_info();
                }
            }

            if (isset($this->smarty)) {
                $this->smarty->assign('ecs_session', session()->all());
            }
        }

        /**
         * 判断是否支持 Gzip 模式
         */
        if (!defined('INIT_NO_SMARTY') && gzip_enabled()) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        define('__ROOT__', asset('/'));
        define('__PUBLIC__', asset('/vendor'));
        define('__TPL__', asset('/themes/' . $GLOBALS['_CFG']['template']));
    }
}
