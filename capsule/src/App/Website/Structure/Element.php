<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 23.05.2014 7:04:34 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Website\Structure;

/**
 * Element.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Element
{
    /**
     * ID key
     * 
     * @var string
     */
    const ID = 'id';
    
    /**
     * Explicit conversion to a string
     * 
     * @param void
     * @return void
     */
    abstract public function toString();
    
    /**
     * Internal data
     *
     * @var array
     */
    protected $data;
    
    /**
     * Disable create object directly from outside
     *
     * @param void
     * @return self
     */
    protected function __construct(array $data) {
        $this->data = $data;
        $this->_init($data);
    }
    
    /**
     * stub
     *
     * @param void
     * @return void
     */
    protected function _init(array $data) {}
    
    /**
     * Getter
     *
     * @param string $name
     * @return Ambigous <NULL, multitype:>
     */
    public function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param unknown $value
     * @throws \Exception
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
            return $this;
        }
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
            throw new \Exception($msg);
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
}