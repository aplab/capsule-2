<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 28.03.2014 6:36:09 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Registry;

use Capsule\I18n\I18n;
/**
 * Registry.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Registry
{
    /**
     * Instances
     *
     * @var array
     */
    protected static $instances = array();
    
    /**
     * Disable create object directly
     *
     * @param void
     * @return self
     */
    final protected function __construct() {}
    
    /**
     * Disable cloning
     *
     * @param void
     * @return void
     * @throws Exception
     */
    public function __clone() {
        $msg = I18n::t('Clone is not allowed.');
        throw new \RuntimeException($msg);
    }
    
    /**
     * Returns instance of registry
     *
     * @param string $classname
     * @return self
     */
    protected static function getInstanceOf($classname) {
        if(!isset(self::$instances[$classname])) {
            self::$instances[$classname] = new $classname;
        }
        return self::$instances[$classname];
    }
    
    /**
     * Returns instance of registry
     *
     * @param void
     * @return self
     */
    public static function getInstance() {
        return self::getInstanceOf(get_called_class());
    }
    
    /**
     * Internal data
     *
     * @var array
     */
    protected $registry = array();
    
    /**
     * Getter
     *
     * @param string $name
     * @return Ambigous <NULL, multitype:>
     */
    public function __get($name) {
        return array_key_exists($name, $this->registry) ? $this->registry[$name] : null;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        $this->registry[$name] = $value;
    }
    
    /**
     * Isset overloading
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return array_key_exists($name, $this->registry);
    }
    
    /**
     * Unset overloading
     *
     * @param string $name
     * @return void
     */
    public function __unset($name) {
        unset($this->registry[$name]);
    }
}