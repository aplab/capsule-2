<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 19.07.2014 8:42:33 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Tools;

use Capsule\Db\Db;
/**
 * Mysql.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Mysql
{
    /**
     * @var Db
     */
    protected static $driver;
    
    public static function driver(Db $driver = null) {
        if (is_null($driver)) {
            if (!self::$driver) {
                self::$driver = Db::getInstance();
            }
        } else {
            self::$driver = $driver;
        }
        return self::$driver;
    }
    
    public static function uuid() {
        return self::driver()->query('SELECT UUID()')->fetch_one();
    }
    
    public static function dateAdd($date, $expr, $unit) {
        $db = self::driver();
        $date = $db->qt($date);
        settype($expr, 'string');
        if (ctype_digit($expr)) {
            $expr = $db->escape_string($expr);
        } else {
            $expr = $db->qt($expr);
        }
        $unit = $db->escape_string(strtoupper($unit));
        return $db->query('SELECT DATE_ADD(' . $date . ', INTERVAL ' . $expr . ' ' . $unit . ')')->fetch_one();
    }
    
    public static function dateSub($date, $expr, $unit) {
        $db = self::driver();
        $date = $db->qt($date);
        settype($expr, 'string');
        if (ctype_digit($expr)) {
            $expr = $db->escape_string($expr);
        } else {
            $expr = $db->qt($expr);
        }
        $unit = $db->escape_string(strtoupper($unit));
        return $db->query('SELECT DATE_SUB(' . $date . ', INTERVAL ' . $expr . ' ' . $unit . ')')->fetch_one();
    }
    
    public static function now($no_query = true) {
        if ($no_query) {
            return date('Y-m-d H:i:s');
        } else {
            return self::driver()->query('SELECT NOW()')->fetch_one();
        }
    }
}