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
     * @param void|string|array|array[[]][]
     * @return $this
     */
    public function lock()
    {
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
        return $this;
    }
    
    /**
     * Lock existing properties
     *
     * @param void
     * @return self
     */
    public function lockExisting()
    {
        $this->lock = array_fill_keys(array_keys($this->registry), true);
        return $this;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if ($this->lockAll) {
            throw new \RuntimeException('Cannot overwrite a property');
        }
        if (array_key_exists($name, $this->lock)) {
            throw new \RuntimeException('Cannot overwrite readonly property: ' . $name);
        }
        $this->registry[$name] = $value;
    }
    
    /**
     * Unset overloading
     *
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        if ($this->lockAll) {
            throw new \RuntimeException('Cannot unset a property');
        }
        if (array_key_exists($name, $this->lock)) {
            throw new \RuntimeException('Cannot unset readonly property: ' . $name);
        }
        unset($this->registry[$name]);
    }
}