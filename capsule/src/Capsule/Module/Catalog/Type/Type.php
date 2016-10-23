<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 19 мая 2015 г. 23:23:20 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module\Catalog\Type;

/**
 * Type.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Type
{
    /**
     * Привязка к namespace
     *
     * @param void
     * @return string
     */
    public static function ns() {
        return __NAMESPACE__;
    }
    
    public static function config() {
        $data = json_decode(static::$json, true, 10, JSON_BIGINT_AS_STRING);
        if (json_last_error()) {
            $msg = json_last_error_msg();
            throw new \Exception($msg);
        }
        return $data;
    }
}