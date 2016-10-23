<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.12.2013 1:03:48 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Table\Columns;

use Capsule\DataModel\Config\AbstractConfig;

/**
 * Columns.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Columns extends AbstractConfig
{
    /**
     * @param array $data
     * @return self
     */
    public function __construct(array $data) {
        foreach ($data as $column_name => $column_data) {
            $this->data[$column_name] = new Column($column_data);
        }
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString($indent = 0) {
        $ret = array();
        foreach ($this->data as $name => $column) {
            $ret[] = str_repeat(' ', $indent) . '`' . $name . '` ' . sprintf($column->toString(), $name);
        }
        return join(', ' . chr(10), $ret);
    }
}