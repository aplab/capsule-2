<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 09.07.2013 21:55:36 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui;

use ReflectionClass, Iterator, Countable;
use Capsule\Exception;
use Capsule\Core\Fn;
use Capsule\I18n\I18n;
use Capsule\Validator\SignedDigits;

/**
 * Section.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Section implements Iterator, Countable
{
    /**
     * Common data
     *
     * @var array
     */
    protected static $common = array();

    /**
     * Object data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Element content
     *
     * @var array
     */
    protected $content = array();
    
    /**
     * Element content with string index (was added with "as" parameter)
     * 
     * @var unknown
     */
    protected $index = array();

    /**
     * defined by Countable interface.
     *
     * @param void
     * @return int
     */
    public function count() {
        return sizeof($this->content);
    }

    /**
     * Local templates directory
     *
     * @var string
     */
    protected static $localTplDir = '/tpl';

    /**
     * Returns element with id
     *
     * @param valid_index $id
     * @return self
     */
    public static function getElementById($id = null) {
        Fn::is_key($id);
        $class = get_called_class();
        if (isset(static::$common[$class]['elements'][$id])) {
            return static::$common[$class]['elements'][$id];
        }
        return null;
    }

    /**
     * Returns all elements
     *
     * @param void
     * @return array
     */
    public static function all() {
        $class = get_called_class();
        if (!isset(static::$common[$class]['elements'])) {
            static::$common[$class]['elements'] = array();
        }
        return static::$common[$class]['elements'];
    }

    /**
     * Setter
     *
     * @param string $name
     * @param multitype $value
     * @return Section
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            return $this->$setter($name, $value);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * setter
     *
     * @param string $name
     * @return Ambigous <NULL, multitype:>
     */
    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $getter = 'get' . ucfirst($name);
        if (in_array($getter, get_class_methods($this))) {
            return $this->$getter($name);
        }
        return null;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name) {
        return array_key_exists($name, $this->data);
    }

    /**
     * unset() overloading
     *
     * @param  string $name
     * @return void
     * @throws Exception
     */
    public function __unset($name) {
        $mutator = 'unset' . ucfirst($name);
        if (in_array($mutator, get_class_methods($this))) {
            return $this->$mutator($name);
        }
        unset($this->data[$name]);
        return $this;
    }

    /**
     * Set id
     *
     * @param string $name
     * @param valid_index $value
     * @return self
     */
    protected function setId($name, $id) {
        Fn::is_key($id);
        $class = get_class($this);
        if (isset($this->id)) {
            unset(static::$common[$class]['elements'][$this->id]);
        }
        if (isset(static::$common[$class]['elements'][$id])) {
            // Element with this id already exists
            $msg = I18n::t('Id already in use');
            throw new Exception($msg);
        }
        $this->data['id'] = $id;
        static::$common[$class]['elements'][$id] = $this;
        return $this;
    }

    /**
     * @param string $name
     * @return self
     */
    protected function unsetId($name) {
        $class = get_class($this);
        unset(static::$common[$class]['elements'][$this->id]);
        unset($this->data[$name]);
        return $this;
    }

    /**
     * Append content part
     *
     * @param mixed $content
     * @param string $as
     * @throws Exception
     * @return \Capsule\Ui\Section
     */
    public function append($content, $as = null) {
        if (is_null($as)) {
            $this->content[] = $content;
            return $this;
        } else {
            Fn::is_key($as);
            if (array_key_exists($as, $this->index)) {
                $msg = I18n::t('Key already exists: ') . $as;
                throw new Exception($msg);
            }
            $this->content[] = $content;
            $this->index[$as] = $content;
        }
        return $this;
    }
    
    /**
     * Prepend content part
     *
     * @param mixed $content
     * @param string $as
     * @throws Exception
     * @return \Capsule\Ui\Section
     */
    public function prepend($content, $as = null) {
        if (is_null($as)) {
            array_unshift($this->content, $content);
            return $this;
        } else {
            Fn::is_key($as);
            if (array_key_exists($as, $this->index)) {
                $msg = I18n::t('Key already exists: ') . $as;
                throw new Exception($msg);
            }
            array_unshift($this->content, $content);
            $this->index[$as] = $content;
        }
        return $this;
    }
    
    /**
     * Insertion content part
     * If position is negative, the insertion from the end of content. 
     * 
     * @param mixed $content
     * @param int $position
     * @param string $as
     */
    public function insert($content, $position = null, $as = null) {
        if (!is_null($as)) {
            Fn::is_key($as);
            if (array_key_exists($as, $this->index)) {
                $msg = I18n::t('Key already exists: ') . $as;
                throw new Exception($msg);
            }
            $this->index[$as] = $content;
        }
        if (is_null($position)) {
            $this->content[] = $content;
            return $this;
        }
        $validator = new SignedDigits();
        $validator->name = 'position';
        if ($validator->isValid($position)) {
            $position = $validator->getClean();
        } else {
            $msg = $validator->message;
            throw new Exception($msg);
        }
        settype($position, 'int');
        $length = sizeof($this->content);
        if ($position < 0) $position = $length + $position;
        if ($position < 0) $position = 0;
        if ($position == 0) {
            array_unshift($this->content, $content);
            return $this;
        }
        if ($position > $length) {
            array_push($this->content, $content);
            return $this;
        }
        $c = 0;
        $tmp = $this->content;
        $this->content = array();
        foreach ($tmp as $v) {
            if ($c == $position) {
                $this->content[] = $content;
            }
            $this->content[] = $v;
            $c++;
        }
    }
    
    /**
     * Заменить контент, добавленный как $as
     *
     * @param mixed $content
     * @param string $as
     * @return void
     */
    public function replace($content, $as = null) {
        if (is_null($as)) {
            $this->content[] = $content;
            return $this;
        } else {
            Fn::is_key($as);
            if (array_key_exists($as, $this->index)) {
                $old = $this->index['$as'];
                foreach ($this->content as $k => $v) {
                    if ($old === $v) $this->content[$k] = $content;
                }
                $this->index['$as'] = $content;
            } else {
                $this->index[$as] = $content;
                $this->content[] = $content;
            }
        }
        return $this;
    }
    
    /**
     * Очистить контент
     *
     * @param void
     * @return void
     */
    public function clear() {
        $this->content = array();
        $this->index = array();
    }
    
    /**
     * Найти по ключу
     *
     * @param string $as
     * @return mixed|null
     */
    public function find($as) {
        Fn::is_key($as);
        return array_key_exists($as, $this->index) ? $this->index[$as] : null;
    }
    
    /**
     * Существует ли фрагмент с таким ключом
     *
     * @param unknown $alias
     * @return boolean
     */
    public function exists($as) {
        return array_key_exists($as, $this->index);
    }

    /**
     * Cloning object
     *
     * @param void
     * @return self
     */
    public function __clone() {
        unset($this->data['id']);
    }

    /**
     * Common service functions
     */

    /**
     * Returns full classname (with namespace)
     *
     * @param void
     * @return string
     */
    final protected static function _class() {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] = $class;
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Returns ReflectionClass for called class
     *
     * @param void
     * @return ReflectionClass
     */
    final protected static function _reflectionClass() {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] = new ReflectionClass($class);
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Returns class root directory
     *
     * @param void
     * @return string
     */
    final protected static function _rootDir() {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            self::$common[$class][__FUNCTION__] = str_replace('\\', '/',
                    dirname(self::_reflectionClass()->getFileName()));
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Returns classname without namespaces
     *
     * @param void
     * @return string
     */
    final protected static function _classname() {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $data = explode('\\', $class);
            self::$common[$class][__FUNCTION__] = array_pop($data);
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Implicit conversion to a string
     *
     * @param void
     * @return string
     */
    abstract public function __toString();

    /**
     * defined by Iterator interface functions
     */

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return $this->content[$this->key()];
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->content);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->content);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->content);
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
     * assign element template
     *
     * @param string $name
     * @param string $path
     * @return self
     */
    protected function setTemplate($name, $path) {
        $path = str_replace('\\', '/', $path);
        if (!preg_match('|/|', $path)) {
            $path = Fn::concat_ws('/', self::_rootDir() . static::$localTplDir, $path);
        }
        if (file_exists($path)) {
            $this->data[$name] = $path;
            return $this;
        }
        $msg = I18n::t('File not found');
        throw new Exception($msg);
    }
}