<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 28.03.2014 7:31:48 YEKT 2014                                              |
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
 * Immutable.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Lockable extends Registry
{
    /**
     * Selective lock
     *
     * @var array
     */
    protected $lock = array();
    
    /**
     * Lock all
     *
     * @var boolean
     */
    protected $lockAll = false;
    
    /**
     * Lock all or selected properties
     *
     * @param void|string|array|multidimensional array
     */
    public function lock() {
        if (!func_num_args()) {
            $this->lockAll = true;
            return $this;
        }
        $args = func_get_args();
        $a = function(array $args) use (&$a)  {
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    $a($arg);
                } else {
                    $this->lock[$arg] = true;
                }
            }
        };
        $a($args);
    }
    
    /**
     * Lock existing properties
     *
     * @param void
     * @return self
     */
    public function lockExisting() {
        $this->lock = array_fill_keys(array_keys($this->registry), true);
        return $this;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        if ($this->lockAll) {
            $msg = I18n::t('Cannot overwrite a property');
            throw new \RuntimeException($msg);
        }
        if (array_key_exists($name, $this->lock)) {
            $msg = I18n::t('Cannot overwrite readonly property: ' . $name);
            throw new \RuntimeException($msg);
        }
        $this->registry[$name] = $value;
    }
    
    /**
     * Unset overloading
     *
     * @param string $name
     * @return void
     */
    public function __unset($name) {
        if ($this->lockAll) {
            $msg = I18n::t('Cannot unset a property');
            throw new \RuntimeException($msg);
        }
        if (array_key_exists($name, $this->lock)) {
            $msg = I18n::t('Cannot unset readonly property: ' . $name);
            throw new \RuntimeException($msg);
        }
        unset($this->registry[$name]);
    }
}