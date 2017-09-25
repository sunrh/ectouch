<?php

namespace App\Modules\Installer\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IndexController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $env_items = array();

    private $dirfile_items = array(
        array('type' => 'dir', 'path' => 'data'),
        array('type' => 'dir', 'path' => 'install'),
    );

    private $func_items = array(
        array('name' => 'mysql_connect'),
        array('name' => 'fsockopen'),
        array('name' => 'gethostbyname'),
        array('name' => 'file_get_contents'),
        array('name' => 'mb_convert_encoding'),
        array('name' => 'json_encode'),
        array('name' => 'curl_init'),
    );

    public function __construct()
    {
        if (is_file('lock') && $_GET['step'] != 5) {
            @header("Content-type: text/html; charset=UTF-8");
            echo "系统已经安装过了，如果要重新安装，那么请删除install目录下的lock文件";
            exit;
        }

        $html_title = 'ECTouch 程序安装向导';
        $html_header = <<<EOF
<div class="header">
  <div class="layout">
    <div class="title">
      <h5>ECTouch 电商系统</h5>
      <h2>系统安装向导</h2>
    </div>
    <div class="version">版本: 2017.04.12.0001</div>
  </div>
</div>
EOF;

        $html_footer = <<<EOF
<div class="footer">
  <h5>Powered by <font class="blue">ECTouch</font><font class="orange"></font></h5>
  <h6>版权所有 2016-2018 &copy; <a href="https://www.ectouch.cn" target="_blank">ECTouch</a></h6>
</div>
EOF;
    }

    public function index(Request $request)
    {
        $step = $request->get('step', 0);
        if (!in_array($step, array(1, 2, 3, 4, 5))) {
            $step = 0;
        }
        switch ($step) {
            case 1:
                $this->env_check($this->env_items);
                $this->dirfile_check($$this->dirfile_items);
                $this->function_check($$this->func_items);
                break;
            case 3:
                $install_error = '';
                $install_recover = '';
                $demo_data = file_exists('./data/utf8_add.sql') ? true : false;
                $this->step3($install_error, $install_recover);
                break;
            case 4:

                break;
            case 5:
                $sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
                $sitepath = str_replace('install', "", $sitepath);
                $auto_site_url = strtolower('http://' . $_SERVER['HTTP_HOST'] . $sitepath);
                break;
            default:
                # code...
                break;
        }

        return $this->display('step_' . $step);
    }

    private function display($view)
    {
        $path = dirname(__DIR__) . '/Views/' . $view . '.php';
        return view($path);
    }

    private function step3(&$install_error, &$install_recover)
    {
        if ($_POST['submitform'] != 'submit') return;
        $db_host = $_POST['db_host'];
        $db_port = $_POST['db_port'];
        $db_user = $_POST['db_user'];
        $db_pwd = $_POST['db_pwd'];
        $db_name = $_POST['db_name'];
        $db_prefix = $_POST['db_prefix'];
        $admin = $_POST['admin'];
        $password = $_POST['password'];
        if (!$db_host || !$db_port || !$db_user || !$db_pwd || !$db_name || !$db_prefix || !$admin || !$password) {
            $install_error = '输入不完整，请检查';
        }
        if (strpos($db_prefix, '.') !== false) {
            $install_error .= '数据表前缀为空，或者格式错误，请检查';
        }

        if (strlen($admin) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^游客|^Guest/is", $admin)) {
            $install_error .= '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字';
        }
        if ($install_error != '') reutrn;
        $mysqli = @ new mysqli($db_host, $db_user, $db_pwd, '', $db_port);
        if ($mysqli->connect_error) {
            $install_error = '数据库连接失败';
            return;
        }

        if ($mysqli->get_server_info() > '5.0') {
            $mysqli->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET " . DBCHARSET);
        } else {
            $install_error = '数据库必须为MySQL5.0版本以上';
            return;
        }
        if ($mysqli->error) {
            $install_error = $mysqli->error;
            return;
        }
        if ($_POST['install_recover'] != 'yes' && ($query = $mysqli->query("SHOW TABLES FROM $db_name"))) {
            while ($row = mysqli_fetch_array($query)) {
                if (preg_match("/^$db_prefix/", $row[0])) {
                    $install_error = '数据表已存在，继续安装将会覆盖已有数据';
                    $install_recover = 'yes';
                    return;
                }
            }
        }

        require('step_4.php');
        $sitepath = strtolower(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
        $sitepath = str_replace('install', "", $sitepath);
        $auto_site_url = strtolower('http://' . $_SERVER['HTTP_HOST'] . $sitepath);
        write_config($auto_site_url);

        $_charset = strtolower(DBCHARSET);
        $mysqli->select_db($db_name);
        $mysqli->set_charset($_charset);
        $sql = file_get_contents("data/{$_charset}.sql");

        if ($_POST['demo_data'] == '1') {
            $sql .= file_get_contents("data/{$_charset}_add.sql");
        }
        $sql = str_replace("\r\n", "\n", $sql);
        runquery($sql, $db_prefix, $mysqli);
        showjsmessage('初始化数据 ... 成功');

        /**
         * ת��
         */
        $sitename = $_POST['site_name'];
        $username = $_POST['admin'];
        $password = $_POST['password'];
        /**
         * 产生随机的md5_key，来替换系统默认的md5_key值
         */
        $md5_key = md5(random(4) . substr(md5($_SERVER['SERVER_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $db_host . $db_user . $db_pwd . $db_name . substr(time(), 0, 6)), 8, 6) . random(10));
        //$mysqli->query("UPDATE {$db_prefix}setting SET value='".$sitename."' WHERE name='site_name'");

        //管理员账号密码
        $mysqli->query("INSERT INTO {$db_prefix}admin (`admin_id`,`admin_name`,`admin_password`,`admin_login_time`,`admin_login_num`,`admin_is_super`) VALUES ('1','$username','" . md5($password) . "', '" . time() . "' ,'0',1);");

        //测试数据
        if ($_POST['demo_data'] == '1') {
            $sql .= file_get_contents("data/{$_charset}_add.sql");
        }
        //新增一个标识文件，用来屏蔽重新安装
        $fp = @fopen('lock', 'wb+');
        @fclose($fp);
        exit("<script type=\"text/javascript\">document.getElementById('install_process').innerHTML = '��װ��ɣ���һ��...';document.getElementById('install_process').href='index.php?step=5&sitename={$sitename}&username={$username}&password={$password}';</script>");
        exit();
    }

//execute sql
    private function runquery($sql, $db_prefix, $mysqli)
    {
//  global $lang, $tablepre, $db;
        if (!isset($sql) || empty($sql)) return;
        $sql = str_replace("\r", "\n", str_replace('#__', $db_prefix, $sql));
        $ret = array();
        $num = 0;
        foreach (explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach ($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
            }
            $num++;
        }
        unset($sql);
        foreach ($ret as $query) {
            $query = trim($query);
            if ($query) {
                if (substr($query, 0, 12) == 'CREATE TABLE') {
                    $line = explode('`', $query);
                    $data_name = $line[1];
                    showjsmessage('数据表  ' . $data_name . ' ... 创建成功');
                    $mysqli->query(droptable($data_name));
                    $mysqli->query($query);
                    unset($line, $data_name);
                } else {
                    $mysqli->query($query);
                }
            }
        }
    }

//�׳�JS��Ϣ
    private function showjsmessage($message)
    {
        echo '<script type="text/javascript">showmessage(\'' . addslashes($message) . ' \');</script>' . "\r\n";
        flush();
        ob_flush();
    }

//д��config�ļ�
    private function write_config($url)
    {
        extract($GLOBALS, EXTR_SKIP);
        $config = 'data/config.php';
        $configfile = @file_get_contents($config);
        $configfile = trim($configfile);
        $configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;
        $charset = 'UTF-8';
        $db_host = $_POST['db_host'];
        $db_port = $_POST['db_port'];
        $db_user = $_POST['db_user'];
        $db_pwd = $_POST['db_pwd'];
        $db_name = $_POST['db_name'];
        $db_prefix = $_POST['db_prefix'];
        $admin = $_POST['admin'];
        $password = $_POST['password'];
        $db_type = 'mysql';
        $cookie_pre = strtoupper(substr(md5(random(6) . substr($_SERVER['HTTP_USER_AGENT'] . md5($_SERVER['SERVER_ADDR'] . $db_host . $db_user . $db_pwd . $db_name . substr(time(), 0, 6)), 8, 6) . random(5)), 0, 4)) . '_';
        $configfile = str_replace("===url===", $url, $configfile);
        $configfile = str_replace("===db_prefix===", $db_prefix, $configfile);
        $configfile = str_replace("===db_charset===", $charset, $configfile);
        $configfile = str_replace("===db_host===", $db_host, $configfile);
        $configfile = str_replace("===db_user===", $db_user, $configfile);
        $configfile = str_replace("===db_pwd===", $db_pwd, $configfile);
        $configfile = str_replace("===db_name===", $db_name, $configfile);
        $configfile = str_replace("===db_port===", $db_port, $configfile);
        @file_put_contents('../conf/config.php', $configfile);
    }


    /**
     * environmental check
     */
    private function env_check(&$env_items)
    {
        $env_items[] = array('name' => '操作系统', 'min' => '无限制', 'good' => 'linux', 'cur' => PHP_OS, 'status' => 1);
        $env_items[] = array('name' => 'PHP版本', 'min' => '5.3', 'good' => '5.3', 'cur' => PHP_VERSION, 'status' => (PHP_VERSION < 5.3 ? 0 : 1));
        $tmp = function_exists('gd_info') ? gd_info() : array();
        preg_match("/[\d.]+/", $tmp['GD Version'], $match);
        unset($tmp);
        $env_items[] = array('name' => 'GD库', 'min' => '2.0', 'good' => '2.0', 'cur' => $match[0], 'status' => ($match[0] < 2 ? 0 : 1));
        $env_items[] = array('name' => '附件上传', 'min' => '未限制', 'good' => '2M', 'cur' => ini_get('upload_max_filesize'), 'status' => 1);
        $disk_place = function_exists('disk_free_space') ? floor(disk_free_space(ROOT_PATH) / (1024 * 1024)) : 0;
        $env_items[] = array('name' => '磁盘空间', 'min' => '100M', 'good' => '>100M', 'cur' => empty($disk_place) ? '未知' : $disk_place . 'M', 'status' => $disk_place < 100 ? 0 : 1);
    }

    /**
     * file check
     */
    private function dirfile_check(&$dirfile_items)
    {
        foreach ($dirfile_items as $key => $item) {
            $item_path = '/' . $item['path'];
            if ($item['type'] == 'dir') {
                if (!dir_writeable(ROOT_PATH . $item_path)) {
                    if (is_dir(ROOT_PATH . $item_path)) {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nodir';
                    }
                } else {
                    $dirfile_items[$key]['status'] = 1;
                    $dirfile_items[$key]['current'] = '+r+w';
                }
            } else {
                if (file_exists(ROOT_PATH . $item_path)) {
                    if (is_writable(ROOT_PATH . $item_path)) {
                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                    } else {
                        $dirfile_items[$key]['status'] = 0;
                        $dirfile_items[$key]['current'] = '+r';
                    }
                } else {
                    if ($fp = @fopen(ROOT_PATH . $item_path, 'wb+')) {
                        $dirfile_items[$key]['status'] = 1;
                        $dirfile_items[$key]['current'] = '+r+w';
                        @fclose($fp);
                        @unlink(ROOT_PATH . $item_path);
                    } else {
                        $dirfile_items[$key]['status'] = -1;
                        $dirfile_items[$key]['current'] = 'nofile';
                    }
                }
            }
        }
    }

    /**
     * dir is writeable
     * @return number
     */
    private function dir_writeable($dir)
    {
        $writeable = 0;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755);
        } else {
            @chmod($dir, 0755);
        }
        if (is_dir($dir)) {
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        return $writeable;
    }

    /**
     * function is exist
     */
    private function function_check(&$func_items)
    {
        $func = array();
        foreach ($func_items as $key => $item) {
            $func_items[$key]['status'] = function_exists($item['name']) ? 1 : 0;
        }
    }

    private function show_msg($msg)
    {
        global $html_title, $html_header, $html_footer;
        include 'step_msg.php';
        exit();
    }

//make rand
    private function random($length, $numeric = 0)
    {
        $seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * drop table
     */
    private function droptable($table_name)
    {
        return "DROP TABLE IF EXISTS `" . $table_name . "`;";
    }
}