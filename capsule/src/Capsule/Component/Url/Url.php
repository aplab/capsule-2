<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 11.05.2014 8:52:17 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Url;

/**
 * Url.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $scheme
 * @property string $host
 * @property string $path
 */
class Url
{
    protected $data = array();
    
    /**
     * getter
     *
     * @param $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (in_array($getter, get_class_methods($this))) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
    
    /**
     * setter
     *
     * @param $name
     * @param $value
     * @throws Exception
     * @return mixed
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
            return $this;
        }
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * Absolute
     *
     * @param void
     * @return string
     */
    public function abs() {
        $ret = $this->scheme . '://' . $this->host . '/' . ltrim($this->path, '/');
    }
    
    /**
     * Relative
     *
     * @param void
     * @return string
     */
    public function rel() {
        return $this->path;
    }
}