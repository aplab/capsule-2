<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 02.07.2013 22:43:14 YEKT 2013                                             |
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

use Countable, Iterator;
use Capsule\Core\Singleton;
use Capsule\Exception;

/**
 * Path.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 * @property array $data
 */
class Path extends Singleton implements Countable, Iterator
{
    /**
     * internal data
     *
     * @var array
     */
    protected $data;

    /**
     * Number of elements in configuration data.
     *
     * @var integer
     */
    protected $count;

    /**
     * raw path
     *
     * @var string
     */
    private $_path;

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() {
        if (is_null($this->count)) {
            $this->count = sizeof($this->getData());
        }
        return $this->count;
    }

    /**
     * Возвращает части строки запроса в виде массива
     *
     * @param void
     * @return array
     */
    protected function getData() {
        if (is_null($this->data)) {
            $this->data = array();
            $request_uri = getenv('REQUEST_URI');
            if ($request_uri) {
                $path = parse_url($request_uri, PHP_URL_PATH);
                if (false === $path) {
                    $msg = 'Seriously malformed URL';
                    throw new Exception($msg);
                }
                $this->_path = $path;
                $data = explode('/', trim($path, '/'));
                if (is_array($data)) {
                    if (0 === strlen(join($data))) {
                        $data = array();
                    }
                    $this->data = $data;
                }
            }
        }
        return $this->data;
    }

    /**
     * Возвращает значение свойства.
     *
     * @param $name
     * @throws Exception
     * @return mixed
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (in_array($getter, get_class_methods($this))) {
            return $this->$getter();
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return $this->data[$this->key()];
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->data);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->data);
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
     * returns current path
     *
     * @return string
     */
    public function __toString() {
        return $this->_path;
    }

    /**
     * returns current path
     *
     * @return string
     */
    public static function path() {
        return static::getInstance()->__toString();
    }
}