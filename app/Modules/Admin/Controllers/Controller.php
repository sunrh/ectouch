<?php

namespace App\Modules\Admin\Controllers;

use App\Libraries\Captcha;
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

/**
 * Class Controller
 * @package App\Modules\Admin\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $ecs;
    protected $db;
    protected $err;
    protected $sess;
    protected $smarty;
    protected $_CFG;

    public function __construct(Request $request)
    {
        define('ECS_ADMIN', true);

        $_GET = $request->query() + $request->route()->parameters();
        $_POST = $request->post();
        $_REQUEST = $_GET + $_POST;
        $_REQUEST['act'] = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'list';

        load_helper(['time', 'base', 'common']);
        load_helper(['main', 'exchange'], 'admin');

        $this->ecs = $GLOBALS['ecs'] = new Shop();
        define('DATA_DIR', $this->ecs->data_dir());
        define('IMAGE_DIR', $this->ecs->image_dir());

        /* 初始化数据库类 */
        $this->db = $GLOBALS['db'] = new Mysql();

        /* 创建错误处理对象 */
        $this->err = $GLOBALS['err'] = new Error('message.htm');

        /* 初始化session */
        // $this->sess = $GLOBALS['sess'] = new Session($this->db, $this->ecs->table('sessions'), $this->ecs->table('sessions_data'), 'ECSCP_ID');

        /* 载入系统参数 */
        $this->_CFG = $GLOBALS['_CFG'] = load_config();

        // TODO : 登录部分准备拿出去做，到时候把以下操作一起挪过去
        if ($_REQUEST['act'] == 'captcha') {
            $img = new Captcha(public_path('data/captcha/'));
            @ob_end_clean(); //清除之前出现的多余输入
            $img->generate_image();
            exit;
        }

        load_lang(['common', 'log_action', getCurrentName()], 'admin');

        /* 创建 Smarty 对象。*/
        $this->smarty = $GLOBALS['smarty'] = new Template();
        $this->smarty->template_dir = dirname(__DIR__) . '/views';
        $this->smarty->compile_dir = storage_path('temp/compiled/admin');
        if (config('app.debug')) {
            $this->smarty->force_compile = true;
        }

        $this->smarty->assign('lang', $GLOBALS['_LANG']);
        $this->smarty->assign('help_open', $GLOBALS['_CFG']['help_open']);
        $this->smarty->assign('enable_order_check', $GLOBALS['_CFG']['enable_order_check']);

        /* 验证管理员身份 */
        if ((!session()->has('admin_id') || intval(session('admin_id')) <= 0) &&
            $_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
            $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order') {
            /* session 不存在，检查cookie */
            if (!empty(request()->cookie('ectouch_cp_admin_id')) && !empty(request()->cookie('ectouch_cp_admin_pass'))) {
                // 找到了cookie, 验证cookie信息
                $sql = 'SELECT user_id, user_name, password, action_list, last_login ' .
                    ' FROM ' . $this->ecs->table('admin_user') .
                    " WHERE user_id = '" . intval(request()->cookie('ectouch_cp_admin_id')) . "'";
                $row = $this->db->GetRow($sql);

                if (!$row) {
                    // 没有找到这个记录
                    cookie()->queue('ectouch_cp_admin_id', '', 1);
                    cookie()->queue('ectouch_cp_admin_pass', '', 1);

                    if (!empty($_REQUEST['is_ajax'])) {
                        make_json_error($GLOBALS['_LANG']['priv_error']);
                    } else {
                        ecs_header("Location: privilege.php?act=login\n");
                    }

                    exit;
                } else {
                    // 检查密码是否正确
                    if (md5($row['password'] . $GLOBALS['_CFG']['hash_code']) == request()->cookie('ectouch_cp_admin_pass')) {
                        !isset($row['last_time']) && $row['last_time'] = '';
                        set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['last_time']);

                        // 更新最后登录时间和IP
                        $this->db->query('UPDATE ' . $this->ecs->table('admin_user') .
                            " SET last_login = '" . gmtime() . "', last_ip = '" . real_ip() . "'" .
                            " WHERE user_id = '" . session('admin_id') . "'");
                    } else {
                        cookie()->queue('ectouch_cp_admin_id', '', 1);
                        cookie()->queue('ectouch_cp_admin_pass', '', 1);

                        if (!empty($_REQUEST['is_ajax'])) {
                            make_json_error($GLOBALS['_LANG']['priv_error']);
                        } else {
                            ecs_header("Location: privilege.php?act=login\n");
                        }

                        exit;
                    }
                }
            } else {
                if (!empty($_REQUEST['is_ajax'])) {
                    make_json_error($GLOBALS['_LANG']['priv_error']);
                } else {
                    ecs_header("Location: privilege.php?act=login\n");
                }

                exit;
            }
        }

        $this->smarty->assign('token', $GLOBALS['_CFG']['token']);

        //header('Cache-control: private');
        header('content-type: text/html; charset=' . CHARSET);
        header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        /* 判断是否支持gzip模式 */
        if (gzip_enabled()) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        define('__ROOT__', asset('/'));
        define('__PUBLIC__', asset('vendor'));
        define('__TPL__', asset('vendor/admin'));
    }
}
