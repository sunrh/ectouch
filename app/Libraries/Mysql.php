<?php

namespace App\Libraries;

use Yii;

/**
 * MYSQL 公用类库
 * Class Mysql
 * @package App\Libraries
 */
class Mysql
{
    public function version()
    {
        return $this->getOne("select version() as ver");
    }

    public function error()
    {
        return '1';
    }

    public function errno()
    {
        return false;
    }

    public function errorMsg()
    {
        return false;
    }

    public function insert_id()
    {
        return Yii::$app->db->getLastInsertID();
    }

    public function query($sql)
    {
        $m = strtolower(substr(ltrim(trim($sql), '('), 0, 6));
        if ($m == 'select' || substr($m, 0, 4) == 'desc' || substr($m, 0, 4) == 'show') {
            $res = Yii::$app->db->createCommand($sql)->queryAll();
        } else {
            $res = Yii::$app->db->createCommand($sql)->execute();
        }

        return $res;
    }

    public function selectLimit($sql, $num, $start = 0)
    {
        if ($start == 0) {
            $sql .= ' LIMIT ' . $num;
        } else {
            $sql .= ' LIMIT ' . $start . ', ' . $num;
        }

        return $this->query($sql);
    }

    public function getOne($sql, $limited = false)
    {
        if ($limited == true) {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if (!empty($res)) {
            return reset($res[0]);
        } else {
            return false;
        }
    }

    public function getAll($sql)
    {
        return $this->query($sql);
    }

    public function getRow($sql, $limited = false)
    {
        if ($limited == true) {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if (!empty($res)) {
            return $res[0];
        } else {
            return false;
        }
    }

    public function getCol($sql)
    {
        $res = $this->query($sql);
        if (!empty($res)) {
            $arr = [];
            foreach ($res as $row) {
                $arr[] = reset($row);
            }

            return $arr;
        }

        return $res;
    }

    public function getColCached($sql)
    {
        $cache_id = md5($sql);
        $res = cache($cache_id);
        if ($res === false) {
            $res = $this->getCol($sql);
            cache($cache_id, $res);
        }
        return $res;
    }

    public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '')
    {
        $field_names = $this->getCol('DESC ' . $table);

        $sql = '';
        if ($mode == 'INSERT') {
            $fields = $values = [];
            foreach ($field_names as $value) {
                if (array_key_exists($value, $field_values) == true) {
                    $fields[] = $value;
                    $values[] = "'" . $field_values[$value] . "'";
                }
            }

            if (!empty($fields)) {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        } else {
            $sets = [];
            foreach ($field_names as $value) {
                if (array_key_exists($value, $field_values) == true) {
                    $sets[] = $value . " = '" . $field_values[$value] . "'";
                }
            }

            if (!empty($sets)) {
                $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
            }
        }

        if ($sql) {
            return $this->query($sql);
        } else {
            return false;
        }
    }

    public function autoReplace($table, $field_values, $update_values, $where = '')
    {
        $field_descs = $this->getAll('DESC ' . $table);

        $primary_keys = [];
        foreach ($field_descs as $value) {
            $field_names[] = $value['Field'];
            if ($value['Key'] == 'PRI') {
                $primary_keys[] = $value['Field'];
            }
        }

        $fields = $values = [];
        foreach ($field_names as $value) {
            if (array_key_exists($value, $field_values) == true) {
                $fields[] = $value;
                $values[] = "'" . $field_values[$value] . "'";
            }
        }

        $sets = [];
        foreach ($update_values as $key => $value) {
            if (array_key_exists($key, $field_values) == true) {
                if (is_int($value) || is_float($value)) {
                    $sets[] = $key . ' = ' . $key . ' + ' . $value;
                } else {
                    $sets[] = $key . " = '" . $value . "'";
                }
            }
        }

        $sql = '';
        if (empty($primary_keys)) {
            if (!empty($fields)) {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        } else {
            if ($this->version() >= '4.1') {
                if (!empty($fields)) {
                    $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                    if (!empty($sets)) {
                        $sql .= 'ON DUPLICATE KEY UPDATE ' . implode(', ', $sets);
                    }
                }
            } else {
                if (empty($where)) {
                    $where = [];
                    foreach ($primary_keys as $value) {
                        if (is_numeric($value)) {
                            $where[] = $value . ' = ' . $field_values[$value];
                        } else {
                            $where[] = $value . " = '" . $field_values[$value] . "'";
                        }
                    }
                    $where = implode(' AND ', $where);
                }

                if ($where && (!empty($sets) || !empty($fields))) {
                    if (intval($this->getOne("SELECT COUNT(*) FROM $table WHERE $where")) > 0) {
                        if (!empty($sets)) {
                            $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
                        }
                    } else {
                        if (!empty($fields)) {
                            $sql = 'REPLACE INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
                        }
                    }
                }
            }
        }

        if ($sql) {
            return $this->query($sql);
        } else {
            return false;
        }
    }
}
