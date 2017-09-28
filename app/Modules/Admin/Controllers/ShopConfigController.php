<?php

namespace App\Modules\Admin\Controllers;

/**
 * 管理中心商店设置
 * Class ShopConfigController
 * @package App\Modules\Admin\Controllers
 */
class ShopConfigController extends Controller
{
    public function actionIndex()
    {
        /**
         * 列表编辑 ?act=list_edit
         */
        if ($_REQUEST['act'] == 'list_edit') {
            /* 检查权限 */
            admin_priv('shop_config');

            /* 可选语言 */
            $dir = opendir(resource_path('lang'));
            $lang_list = array();
            while (@$file = readdir($dir)) {
                if ($file != '.' && $file != '..' && $file != '.svn' && $file != '_svn' && is_dir(resource_path('lang') . '/' . $file)) {
                    $lang_list[] = $file;
                }
            }
            @closedir($dir);

            $this->smarty->assign('lang_list', $lang_list);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['01_shop_config']);
            $this->smarty->assign('group_list', $this->get_settings(null, array('5')));
            $this->smarty->assign('countries', get_regions());

            if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'iis') !== false) {
                $rewrite_confirm = $GLOBALS['_LANG']['rewrite_confirm_iis'];
            } else {
                $rewrite_confirm = $GLOBALS['_LANG']['rewrite_confirm_apache'];
            }
            $this->smarty->assign('rewrite_confirm', $rewrite_confirm);

            if ($GLOBALS['_CFG']['shop_country'] > 0) {
                $this->smarty->assign('provinces', get_regions(1, $GLOBALS['_CFG']['shop_country']));
                if ($GLOBALS['_CFG']['shop_province']) {
                    $this->smarty->assign('cities', get_regions(2, $GLOBALS['_CFG']['shop_province']));
                }
            }
            $this->smarty->assign('cfg', $GLOBALS['_CFG']);

            $this->smarty->display('shop_config.htm');
        }

        /**
         * 邮件服务器设置
         */
        if ($_REQUEST['act'] == 'mail_settings') {
            /* 检查权限 */
            admin_priv('shop_config');

            $arr = $this->get_settings(array(5));


            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['mail_settings']);
            $this->smarty->assign('cfg', $arr[5]['vars']);
            $this->smarty->display('shop_config_mail_settings.htm');
        }

        /**
         * 提交   ?act=post
         */
        if ($_REQUEST['act'] == 'post') {
            $type = empty($_POST['type']) ? '' : $_POST['type'];

            /* 检查权限 */
            admin_priv('shop_config');

            /* 允许上传的文件类型 */
            $allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|CERT|';

            /* 保存变量值 */
            $count = count($_POST['value']);

            $arr = array();
            $sql = 'SELECT id, value FROM ' . $this->ecs->table('shop_config');
            $res = $this->db->query($sql);
            foreach ($res as $row) {
                $arr[$row['id']] = $row['value'];
            }
            foreach ($_POST['value'] AS $key => $val) {
                if ($arr[$key] != $val) {
                    $sql = "UPDATE " . $this->ecs->table('shop_config') . " SET value = '" . trim($val) . "' WHERE id = '" . $key . "'";
                    $this->db->query($sql);
                }
            }

            /* 处理上传文件 */
            $file_var_list = array();
            $sql = "SELECT * FROM " . $this->ecs->table('shop_config') . " WHERE parent_id > 0 AND type = 'file'";
            $res = $this->db->query($sql);
            foreach ($res as $row) {
                $file_var_list[$row['code']] = $row;
            }

            foreach ($_FILES AS $code => $file) {
                /* 判断用户是否选择了文件 */
                if ((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none')) {
                    /* 检查上传的文件类型是否合法 */
                    if (!check_file_type($file['tmp_name'], $file['name'], $allow_file_types)) {
                        sys_msg(sprintf($GLOBALS['_LANG']['msg_invalid_file'], $file['name']));
                    } else {
                        if ($code == 'shop_logo') {
                            include_once('includes/lib_template.php');
                            $info = get_template_info($GLOBALS['_CFG']['template']);

                            $file_name = str_replace('{$template}', $GLOBALS['_CFG']['template'], $file_var_list[$code]['store_dir']) . $info['logo'];
                        } elseif ($code == 'watermark') {
                            $ext = array_pop(explode('.', $file['name']));
                            $file_name = $file_var_list[$code]['store_dir'] . 'watermark.' . $ext;
                            if (file_exists($file_var_list[$code]['value'])) {
                                @unlink($file_var_list[$code]['value']);
                            }
                        } elseif ($code == 'wap_logo') {
                            $ext = array_pop(explode('.', $file['name']));
                            $file_name = $file_var_list[$code]['store_dir'] . 'wap_logo.' . $ext;
                            if (file_exists($file_var_list[$code]['value'])) {
                                @unlink($file_var_list[$code]['value']);
                            }
                        } else {
                            $file_name = $file_var_list[$code]['store_dir'] . $file['name'];
                        }

                        /* 判断是否上传成功 */
                        if (move_upload_file($file['tmp_name'], $file_name)) {
                            $sql = "UPDATE " . $this->ecs->table('shop_config') . " SET value = '$file_name' WHERE code = '$code'";
                            $this->db->query($sql);
                        } else {
                            sys_msg(sprintf($GLOBALS['_LANG']['msg_upload_failed'], $file['name'], $file_var_list[$code]['store_dir']));
                        }
                    }
                }
            }

            /* 处理发票类型及税率 */
            if (!empty($_POST['invoice_rate'])) {
                foreach ($_POST['invoice_rate'] as $key => $rate) {
                    $rate = round(floatval($rate), 2);
                    if ($rate < 0) {
                        $rate = 0;
                    }
                    $_POST['invoice_rate'][$key] = $rate;
                }
                $invoice = array(
                    'type' => $_POST['invoice_type'],
                    'rate' => $_POST['invoice_rate']
                );
                $sql = "UPDATE " . $this->ecs->table('shop_config') . " SET value = '" . serialize($invoice) . "' WHERE code = 'invoice_type'";
                $this->db->query($sql);
            }

            /* 记录日志 */
            admin_log('', 'edit', 'shop_config');

            /* 清除缓存 */
            clear_all_files();

            $GLOBALS['_CFG'] = load_config();

            $shop_country = $this->db->getOne("SELECT region_name FROM " . $this->ecs->table('region') . " WHERE region_id='{$GLOBALS['_CFG']['shop_country']}'");
            $shop_province = $this->db->getOne("SELECT region_name FROM " . $this->ecs->table('region') . " WHERE region_id='{$GLOBALS['_CFG']['shop_province']}'");
            $shop_city = $this->db->getOne("SELECT region_name FROM " . $this->ecs->table('region') . " WHERE region_id='{$GLOBALS['_CFG']['shop_city']}'");

            $spt = '<script type="text/javascript" src="http://api.ecmoban.com/record.php?';
            $spt .= "url=" . urlencode($this->ecs->url());
            $spt .= "&shop_name=" . urlencode($GLOBALS['_CFG']['shop_name']);
            $spt .= "&shop_title=" . urlencode($GLOBALS['_CFG']['shop_title']);
            $spt .= "&shop_desc=" . urlencode($GLOBALS['_CFG']['shop_desc']);
            $spt .= "&shop_keywords=" . urlencode($GLOBALS['_CFG']['shop_keywords']);
            $spt .= "&country=" . urlencode($shop_country) . "&province=" . urlencode($shop_province) . "&city=" . urlencode($shop_city);
            $spt .= "&address=" . urlencode($GLOBALS['_CFG']['shop_address']);
            $spt .= "&qq={$GLOBALS['_CFG']['qq']}&ww={$GLOBALS['_CFG']['ww']}&ym={$GLOBALS['_CFG']['ym']}&msn={$GLOBALS['_CFG']['msn']}";
            $spt .= "&email={$GLOBALS['_CFG']['service_email']}&phone={$GLOBALS['_CFG']['service_phone']}&icp=" . urlencode($GLOBALS['_CFG']['icp_number']);
            $spt .= "&version=" . VERSION . "&language={$GLOBALS['_CFG']['lang']}&php_ver=" . PHP_VERSION . "&mysql_ver=" . $this->db->version();
            $spt .= "&charset=" . CHARSET;
            $spt .= '"></script>';

            if ($type == 'mail_setting') {
                $links[] = array('text' => $GLOBALS['_LANG']['back_mail_settings'], 'href' => 'shop_config.php?act=mail_settings');
                sys_msg($GLOBALS['_LANG']['mail_save_success'] . $spt, 0, $links);
            } else {
                $links[] = array('text' => $GLOBALS['_LANG']['back_shop_config'], 'href' => 'shop_config.php?act=list_edit');
                sys_msg($GLOBALS['_LANG']['save_success'] . $spt, 0, $links);
            }
        }

        /**
         * 发送测试邮件
         */
        if ($_REQUEST['act'] == 'send_test_email') {
            /* 检查权限 */
            check_authz_json('shop_config');

            /* 取得参数 */
            $email = trim($_POST['email']);

            /* 更新配置 */
            $GLOBALS['_CFG']['mail_service'] = intval($_POST['mail_service']);
            $GLOBALS['_CFG']['smtp_host'] = trim($_POST['smtp_host']);
            $GLOBALS['_CFG']['smtp_port'] = trim($_POST['smtp_port']);
            $GLOBALS['_CFG']['smtp_user'] = json_str_iconv(trim($_POST['smtp_user']));
            $GLOBALS['_CFG']['smtp_pass'] = trim($_POST['smtp_pass']);
            $GLOBALS['_CFG']['smtp_mail'] = trim($_POST['reply_email']);
            $GLOBALS['_CFG']['mail_charset'] = trim($_POST['mail_charset']);

            if (send_mail('', $email, $GLOBALS['_LANG']['test_mail_title'], $GLOBALS['_LANG']['cfg_name']['email_content'], 0)) {
                make_json_result('', $GLOBALS['_LANG']['sendemail_success'] . $email);
            } else {
                make_json_error(join("\n", $this->err->get_all()));
            }
        }

        /**
         * 删除上传文件
         */
        if ($_REQUEST['act'] == 'del') {
            /* 检查权限 */
            check_authz_json('shop_config');

            /* 取得参数 */
            $code = trim($_GET['code']);

            $filename = $GLOBALS['_CFG'][$code];

            //删除文件
            @unlink($filename);

            //更新设置
            $this->update_configure($code, '');

            /* 记录日志 */
            admin_log('', 'edit', 'shop_config');

            /* 清除缓存 */
            clear_all_files();

            sys_msg($GLOBALS['_LANG']['save_success'], 0);

        }
    }

    /**
     * 设置系统设置
     *
     * @param   string $key
     * @param   string $val
     *
     * @return  boolean
     */
    private function update_configure($key, $val = '')
    {
        if (!empty($key)) {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('shop_config') . " SET value='$val' WHERE code='$key'";

            return $GLOBALS['db']->query($sql);
        }

        return true;
    }

    /**
     * 获得设置信息
     *
     * @param   array $groups 需要获得的设置组
     * @param   array $excludes 不需要获得的设置组
     *
     * @return  array
     */
    private function get_settings($groups = null, $excludes = null)
    {

        $config_groups = '';
        $excludes_groups = '';

        if (!empty($groups)) {
            foreach ($groups AS $key => $val) {
                $config_groups .= " AND (id='$val' OR parent_id='$val')";
            }
        }

        if (!empty($excludes)) {
            foreach ($excludes AS $key => $val) {
                $excludes_groups .= " AND (parent_id<>'$val' AND id<>'$val')";
            }
        }

        /* 取出全部数据：分组和变量 */
        $sql = "SELECT * FROM " . $this->ecs->table('shop_config') .
            " WHERE type<>'hidden' $config_groups $excludes_groups ORDER BY parent_id, sort_order, id";
        $item_list = $this->db->getAll($sql);

        /* 整理数据 */
        $group_list = array();
        foreach ($item_list AS $key => $item) {
            $pid = $item['parent_id'];
            $item['name'] = isset($GLOBALS['_LANG']['cfg_name'][$item['code']]) ? $GLOBALS['_LANG']['cfg_name'][$item['code']] : $item['code'];
            $item['desc'] = isset($GLOBALS['_LANG']['cfg_desc'][$item['code']]) ? $GLOBALS['_LANG']['cfg_desc'][$item['code']] : '';

            if ($item['code'] == 'sms_shop_mobile') {
                $item['url'] = 1;
            }
            if ($pid == 0) {
                /* 分组 */
                if ($item['type'] == 'group') {
                    $group_list[$item['id']] = $item;
                }
            } else {
                /* 变量 */
                if (isset($group_list[$pid])) {
                    if ($item['store_range']) {
                        $item['store_options'] = explode(',', $item['store_range']);

                        foreach ($item['store_options'] AS $k => $v) {
                            $item['display_options'][$k] = isset($GLOBALS['_LANG']['cfg_range'][$item['code']][$v]) ?
                                $GLOBALS['_LANG']['cfg_range'][$item['code']][$v] : $v;
                        }
                    }
                    $group_list[$pid]['vars'][] = $item;
                }
            }

        }

        return $group_list;
    }

}