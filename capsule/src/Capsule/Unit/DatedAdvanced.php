<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 17.05.2014 6:32:23 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Unit;

/**
 * DatedAdvanced.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DatedAdvanced extends Unit 
{
    protected function setDatetime($v, $n) {
        \Capsule\Tools\Tools::dump($v);
        $t = new \DateTime($v);\Capsule\Tools\Tools::dump($t);
        \Capsule\Tools\Tools::dump($t);
        $this->data[$n] = $t->format('Y-m-d H:i:s');
        $this->data['date'] = $t->format('Y-m-d');
    }
    
    protected function setDate($v, $n) {
        $msg = 'Cannot set readonly property: ' . get_class($this) . '::$' . $n;
        throw new \Exception($msg);
    }
}