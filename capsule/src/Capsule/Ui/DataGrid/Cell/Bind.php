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

use Capsule\Core\Fn;
/**
 * Bind.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Bind extends Cell
{
    protected static $cache = array();

    protected function options() {
        $hash = spl_object_hash($this);
        if (!array_key_exists($hash, self::$cache)) {
            $class = Fn::cc($this->col->property->bind, Fn::ns($this->col->cell->item));
            self::$cache[$hash] = $class::optionsDataList();
        }
        return self::$cache[$hash];
    }

    public function getValue($id) {
        $options = $this->options();
        return array_key_exists($id, $options) ? $options[$id] : null;
    }
}