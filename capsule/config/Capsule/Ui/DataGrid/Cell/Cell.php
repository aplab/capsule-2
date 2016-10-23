<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 20.01.2014 20:42:50 YEKT 2014                                              |
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

use Capsule\Ui\DataGrid\Col;
use Capsule\DataModel\DataModel;
/**
 * Cell.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Cell implements ICell
{
    protected $data = array();
    
    public function __construct(Col $col) {
        $this->data['col'] = $col;
    }
    
    public function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
            return $this;
        }
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property';
            throw new \RuntimeException($msg);
        }
        $this->data[$name] = $value;
        return $this;
    }
    
    protected function setItem(DataModel $item, $name) {
        $this->data[$name] = $item;
        $this->data['val'] = $item->get($this->col->propertyName);
    }
}