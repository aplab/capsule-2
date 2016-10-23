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
 * @property int $type Тип элемента
 * @property int $order Порядок колонки
 * @property string $tab Название вкладки
 */
class FormElement extends AbstractConfig
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
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     * @return void
     */
    public function __set($name, $value) {
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * getter
     * (non-PHPdoc)
     * @see \Capsule\DataModel\Config\AbstractConfig::__get()
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
}