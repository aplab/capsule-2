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

namespace Capsule\DataModel\Config\Properties;

use Capsule\DataModel\Config\AbstractConfig;

/**
 * Column.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property int $width Ширина колонки
 * @property int $order Порядок колонки
 */
class Column extends AbstractConfig
{
    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        return $this->width;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Set width
     *
     * @param int $value
     * @param string $name
     * @throws \InvalidArgumentException
     * @return \Capsule\DataModel\Config\Properties\Column
     */
    protected function setWidth($value, $name) {
        if (!$value) {
            $this->data[$name] = 0;
            return $this;
        }
        if (ctype_digit((string)$value)) {
            $this->data[$name] = $value;
            return $this;
        }
        $msg = 'Wrong width value';
        throw new \InvalidArgumentException($msg);
    }
}