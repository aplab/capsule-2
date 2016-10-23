<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 04.12.2013 1:23:29 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Table\Indexes\Fields;

use Capsule\DataModel\Config\AbstractConfig;

/**
 * Field.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property boolean $order Сортировка индекса
 * @property int $length Длина индекса
 * @property int $position Порядок поля внутри индекса
 */
class Field extends AbstractConfig
{
    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        $parts = array();
        if (isset($this->length)) {
            $parts[] = '(' . $this->length . ')';
        }
        if (isset($this->order)) {
            $parts[] = strtoupper($this->order);
        }
        return join(' ', $parts);
    }
}