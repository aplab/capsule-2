<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 02.05.2014 15:04:49 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\DataGrid\Cell;

use Capsule\User\User as u;
/**
 * User.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class User extends Cell
{
    protected static $cache;
    
    protected static function users() {
        if (!self::$cache) {
            self::$cache = u::all();
        }
        return self::$cache;
    }
    
    public function getUser($id) {
        $users = self::users();
        return array_key_exists($id, $users) ? $users[$id] : null;
    }
}