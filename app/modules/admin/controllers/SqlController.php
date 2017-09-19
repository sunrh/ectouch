<?php

namespace app\modules\admin\controllers;

/**
 * Class SqlController
 * @package app\modules\admin\controllers
 */
class SqlController extends Controller
{
    public function actionIndex()
    {

        /**
         *  会员管理程序
         */


        $_POST['sql'] = !empty($_POST['sql']) ? trim($_POST['sql']) : '';

        if (!$_POST['sql']) {
            $_REQUEST['act'] = 'main';
        }

        /*------------------------------------------------------ */
//-- 用户帐号列表
        /*------------------------------------------------------ */

        if ($_REQUEST['act'] == 'main') {
            admin_priv('sql_query');

            $this->smarty->assign('type', -1);
            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['04_sql_query']);

            $this->smarty->display('sql.htm');
        }

        if ($_REQUEST['act'] == 'query') {
            admin_priv('sql_query');
            if (!empty($_POST['sql'])) {
                preg_match_all("/(SELECT)/i", $_POST['sql'], $matches);
                if (isset($matches[1]) && count($matches[1]) > 1) {
                    sys_msg("this sql more than one SELECT ");
                }

                if (preg_match("/(UPDATE|DELETE|TRUNCATE|ALTER|DROP|FLUSH|INSERT|REPLACE|SET|CREATE|CONCAT)/i", $_POST['sql'])) {
                    sys_msg("this sql May contain UPDATE,DELETE,TRUNCATE,ALTER,DROP,FLUSH,INSERT,REPLACE,SET,CREATE,CONCAT ");
                }
            }

            $this->assign_sql($_POST['sql']);

            $this->smarty->assign('ur_here', $GLOBALS['_LANG']['04_sql_query']);

            $this->smarty->display('sql.htm');
        }
    }

    /**
     *
     *
     * @access  public
     * @param
     *
     * @return void
     */
    private function assign_sql($sql)
    {
        $sql = stripslashes($sql);
        $this->smarty->assign('sql', $sql);

        /* 解析查询项 */
        $sql = str_replace("\r", '', $sql);
        $query_items = explode(";\n", $sql);
        foreach ($query_items as $key => $value) {
            if (empty($value)) {
                unset($query_items[$key]);
            }
        }
        /* 如果是多条语句，拆开来执行 */
        if (count($query_items) > 1) {
            foreach ($query_items as $key => $value) {
                if ($this->db->query($value, 'SILENT')) {
                    $this->smarty->assign('type', 1);
                } else {
                    $this->smarty->assign('type', 0);
                    $this->smarty->assign('error', $this->db->error());
                    return;
                }
            }
            return; //退出函数
        }

        /* 单独一条sql语句处理 */
        if (preg_match("/^(?:UPDATE|DELETE|TRUNCATE|ALTER|DROP|FLUSH|INSERT|REPLACE|SET|CREATE)\\s+/i", $sql)) {
            if ($this->db->query($sql, 'SILENT')) {
                $this->smarty->assign('type', 1);
            } else {
                $this->smarty->assign('type', 0);
                $this->smarty->assign('error', $this->db->error());
            }
        } else {
            $data = $this->db->GetAll($sql);
            if ($data === false) {
                $this->smarty->assign('type', 0);
                $this->smarty->assign('error', $this->db->error());
            } else {
                $result = '';
                if (is_array($data) && isset($data[0]) === true) {
                    $result = "<table> \n <tr>";
                    $keys = array_keys($data[0]);
                    for ($i = 0, $num = count($keys); $i < $num; $i++) {
                        $result .= "<th>" . $keys[$i] . "</th>\n";
                    }
                    $result .= "</tr> \n";
                    foreach ($data AS $data1) {
                        $result .= "<tr>\n";
                        foreach ($data1 AS $value) {
                            $result .= "<td>" . $value . "</td>";
                        }
                        $result .= "</tr>\n";
                    }
                    $result .= "</table>\n";
                } else {
                    $result = "<center><h3>" . $GLOBALS['_LANG']['no_data'] . "</h3></center>";
                }

                $this->smarty->assign('type', 2);
                $this->smarty->assign('result', $result);
            }
        }
    }
}