<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 22.01.2013 7:00:52 YEKT 2013                                             |
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

use Capsule\Common\Exception;
use Capsule\I18n\I18n;
/**
 * Toolbar.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Toolbar implements \Iterator, \Countable, \JsonSerializable
{
    /**
     * Object instances
     *
     * @var array
     */
    private static $instances = array();
    
    /**
     * Internal data
     *
     * @var array
     */
    private $data = array(
    	'items' => array()
    );
    
    /**
     * constructor
     *
     * @param string $instance_name
     * @throws Exception
     * @return self
     */
    public function __construct($instance_name) {
        if (array_key_exists($instance_name, self::$instances)) {
            $msg = 'Instance name "' . $instance_name . '" already exists';
            throw new \InvalidArgumentException($msg);
        }
        self::$instances[$instance_name] = $this;
        $this->data['instanceName'] = $instance_name;
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
     * Add item
     *
     * @param Button|Delimiter $item
     * @return self
     */
    public function add(Item $item) {
        $this->data['items'][] = $item;
        return $this;
    }
    
    /**
     * @param mixed $value
     * @param string $name
     * @throws \RuntimeException
     */
    protected function setInstanceName($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * @param mixed $value
     * @param string $name
     * @throws \RuntimeException
     */
    protected function setItems($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() {
        return sizeof($this->data['items']);
    }
    
    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return current($this->data['items']);
    }
    
    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->data['items']);
    }
    
    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->data['items']);
    }
    
    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->data['items']);
    }
    
    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid() {
        return ($this->key() !== null);
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