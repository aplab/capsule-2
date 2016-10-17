<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:35:19 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Db;

use mysqli_result;

/**
 * DbResult.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Result extends mysqli_result
{
    /**
     * Получить значение первого столбца первой строки результата
     *
     * @return int|NULL
     */
    public function fetch_one()
    {
        $row = $this->fetch_row();
        if (is_array($row) && isset($row[0])) {
            return $row[0];
        }
        return null;
    }

    /**
     * Выбирает все строки из результирующего набора и помещает их в
     * ассоциативный массив
     *
     * @param void
     * @return array
     */
    public function fetch_assoc_all()
    {
        return $this->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Выбирает все строки из результирующего набора и помещает их в object
     *
     * @param string
     * @param array $params
     * @return array
     */
    public function fetch_object_all($class = null, array $params = null)
    {
        $ret = array();
        if (is_null($class) && is_null($params)) {
            $row = $this->fetch_object();
            while ($row) {
                $ret[] = $row;
                $row = $this->fetch_object();
            }
            return $ret;
        }
        if (is_null($params)) {
            $row = $this->fetch_object($class);
            while ($row) {
                $ret[] = $row;
                $row = $this->fetch_object($class);
            }
            return $ret;
        }
        $row = $this->fetch_object($class, $params);
        while ($row) {
            $ret[] = $row;
            $row = $this->fetch_object($class, $params);
        }
        return $ret;
    }

    /**
     * Возвращает определенный столбец
     *
     * @param void
     * @return array|false
     */
    public function fetch_col($n = null)
    {
        $n = $n ?: 0;
        $ret = array();
        $row = $this->fetch_row();
        while ($row) {
            $ret[] = $row[$n];
            $row = $this->fetch_row();
        }
        return $ret;
    }

    /**
     * Возвращает первую строку результата
     *
     * @param void
     * @return multitype:
     */
    public function fetch_assoc_first()
    {
        return $this->fetch_assoc();
    }

    /**
     * Возвращает массив с ключом $key и значением $value
     * Если $key не задан то обычный индекс по порядку.
     * Если $value не задан то вся строка.
     * Третий параметр если false и при этом задан ключ, то ключа не будет в подмассиве результата
     *
     * @param string $key
     * @param string $value
     * @param string $key_present
     * @return array
     */
    public function fetch_all_index($key = null, $value = null, $key_present = true)
    {
        if (is_null($key) && is_null($value)) {
            return $this->fetch_assoc_all();
        }
        $ret = array();
        if (is_null($value)) {
            $row = $this->fetch_assoc();
            while ($row) {
                $k = $row[$key];
                if (!$key_present) {
                    unset ($row[$key]);
                }
                $ret[$k] = $row;
                $row = $this->fetch_assoc();
            }
            return $ret;
        }
        if (is_null($key)) {
            $row = $this->fetch_assoc();
            while ($row) {
                $ret[] = $row[$value];
                $row = $this->fetch_assoc();
            }
            return $ret;
        }
        $row = $this->fetch_assoc();
        while ($row) {
            $ret[$row[$key]] = $row[$value];
            $row = $this->fetch_assoc();
        }
        return $ret;
    }

    /**
     * @param int
     * @return array
     */
    public function fetch_all($resulttype = MYSQLI_NUM)
    {
        if (method_exists('mysqli_result', 'fetch_all')) return parent::fetch_all($resulttype);# Compatibility layer with PHP < 5.3
        else for ($res = array(); $tmp = $this->fetch_array($resulttype);) $res[] = $tmp;
        return $res;
    }
}