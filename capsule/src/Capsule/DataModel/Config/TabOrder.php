<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 07 июня 2015 г. 0:49:00 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config;

/**
 * TabOrder.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class TabOrder extends AbstractConfig
{
    public function __construct(array $data = array()) {
        foreach ($data as $k => $v) {
            $v = is_scalar($v) ? $v : 0;
            $this->data[$k] = intval(preg_filter('/^-?\\d+$/', '$0', $v)) ?: null;
        }
        asort($this->data);
    }

    public function toString() {
        return json_encode($this->data);
    }
}