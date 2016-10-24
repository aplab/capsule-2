<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 22.01.2014 7:59:04 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Common;

use Capsule\Core\Singleton;
/**
 * TplVar.php
 * Для передачи переменных в шаблон.
 *
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class TplVar extends Singleton
{
    private $data = array();

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            $ret = $this->data[$name];
            unset($this->data[$name]);
            return $ret;
        }
        $msg = 'Undefined property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
        return $this;
    }
}