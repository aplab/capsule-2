<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 24.08.2014 23:18:18 YEKT 2014                                              |
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

use Capsule\Capsule;
/**
 * Sysinfo.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
final class Sysinfo
{
    /**
     * Internal data
     * 
     * @var array
     */
    protected static $data = array();
    
    /**
     * Returns host
     * 
     * @param void
     * @return string
     * @throws Exception
     */
    public static function host() {
        $k = __FUNCTION__;
        if (!array_key_exists($k, self::$data)) {
            self::$data[$k] = Capsule::host();
            if (false !== strpos(self::$data[$k], '/')) {
                $msg = 'Wrong host definition';
                throw new \UnexpectedValueException($msg);
            }
        }
        return self::$data[$k];
    }
    
    /**
     * Returns base url
     *
     * @param void
     * @return string
     * @throws Exception
     */
    public static function baseUrl() {
        $k = __FUNCTION__;
        if (!array_key_exists($k, self::$data)) {
            self::$data[$k] = Capsule::baseUrl();
        }
        return self::$data[$k];
    }
}