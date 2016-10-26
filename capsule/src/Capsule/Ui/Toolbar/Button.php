<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 3:31:39 YEKT 2013                                             |
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
 * Button.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $caption
 * @property string $icon Path to icon file
 * @property string $title
 * @property string $url
 * @property string $action
 * @property boolean $disabled
 * @property array $parameters
 */
class Button extends Item implements \Iterator, \Countable, \JsonSerializable
{
    /**
     * Internal data
     *
     * @var array
     */
    private $data = array(
    	'parameters' => array()
    );
    
    /**
     * Setter
     *
     * @param string $value
     * @param string $name
     */
    protected function setParameters(Parameter $value = null, $name) {
        if (is_null($value)) {
            $this->data['parameters'] = array();
            return $this;
        }
        $this->data['parameters'][] = $value;
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
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() {
        return sizeof($this->data['parameters']);
    }
    
    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return current($this->data['parameters']);
    }
    
    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->data['parameters']);
    }
    
    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->data['parameters']);
    }
    
    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->data['parameters']);
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
     * Add parameter
     *
     * @param Parameter $parameter
     * @return self
     */
    public function add(Parameter $parameter) {
        $this->data['parameters'][] = $parameter;
        return $this;
    }
    
    /**
     * Remove parameter
     *
     * @param Parameter|string $parameter
     * @param string $as
     */
    public function del(Item $item) {
        if ($item instanceof Parameter) {
            $keys = array_keys($this->data['parameters'], $item, true);
            foreach ($keys as $key) {
                unset($this->data['parameters'][$key]);
            }
        }
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