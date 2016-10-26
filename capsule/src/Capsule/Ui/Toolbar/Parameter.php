<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 3:20:48 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\Toolbar;

/**
 * Parameter.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $name
 * @property mixed $value
 * @property boolean $post
 */
class Parameter implements \JsonSerializable
{
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array();
    
    /**
     * @param string $name
     * @return self
     */
    public function __construct($name) {
        $this->data['name'] = $name;
    }
    
    /**
     * getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (in_array($getter, get_class_methods($this))) {
            return $this->$getter($name);
        }
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    /**
     * setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
            return $this;
        }
        $this->data[$name] = $value;
    }
    
    /**
     * Setter
     *
     * @param string $value
     * @param string $name
     */
    protected function setName($value, $name) {
        $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * Задает данные, которые должны быть сериализованы в JSON
     *
     * @param void
     * @return mixed
     */
    public function jsonSerialize() {
        return $this->data;
    }
}