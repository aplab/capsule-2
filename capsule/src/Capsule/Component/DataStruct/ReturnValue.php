<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 0:08:35 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Component\DataStruct;

use Iterator, Countable;
use Capsule\Exception;

/**
 * Config.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class ReturnValue implements Iterator, Countable
{
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = [];

    /**
     * The ability to change data.
     *
     * @var boolean
     */
    protected $lock;

    /**
     * Lock data
     *
     * @param void
     * @return self
     */
    public function lock()
    {
        $this->lock = true;
        return $this;
    }

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count()
    {
        return sizeof($this->data);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * Возвращает значение свойства или значение по умолчанию
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }

    /**
     * Возвращает значение свойства.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    /**
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     * @return self
     */
    public function __set($name, $value)
    {
        if ($this->lock && array_key_exists($name, $this->data)) {
            $msg = 'You can not change the data object is locked';
            throw new Exception($msg);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * unset() overloading
     *
     * @param  string $name
     * @return $this
     * @throws Exception
     */
    public function __unset($name)
    {
        if ($this->lock && array_key_exists($name, $this->data)) {
            $msg = 'You can not unset the data object is locked';
            throw new Exception($msg);
        }
        unset($this->data[$name]);
        return $this;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @param void
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Return an json of the stored data.
     *
     * @param void
     * @return array
     * @throws Exception
     */
    public function toJson($options = null)
    {
        if (is_null($options)) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        }
        $json = json_encode($this->toArray(), $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }
        return $json;
    }
}