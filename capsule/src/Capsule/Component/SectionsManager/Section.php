<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace Capsule\Component\SectionManager;

use Capsule\Component\Path\ComponentTemplatesDir;
use Iterator, Countable;
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
     * Object instances
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Object internal data
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
     * defined by Countable interface.
     *
     * @param void
     * @return int
     */
    public function count()
    {
        return sizeof($this->content);
    }

    /**
     * Возвращает путь к каталогу с шаблонами
     *
     * @return ComponentTemplatesDir
     */
    public static function templatesDir()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(static::$common[$c][$f])) {
            static::$common[$c][$f] = new ComponentTemplatesDir($c);
        }
        return static::$common[$c][$f];
    }

    /**
     * Returns element with id
     *
     * @param string $id
     * @return static
     */
    public static function getElementById($id = null)
    {
        $class = get_called_class();
        if (isset(static::$instances[$class][$id])) {
            return static::$instances[$class][$id];
        }
        return null;
    }

    /**
     * Returns all elements
     *
     * @param void
     * @return array
     */
    public static function all()
    {
        $class = get_called_class();
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = array();
        }
        return static::$instances[$class];
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return Section
     */
    public function __set($name, $value)
    {
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
     * @return mixed
     */
    public function __get($name)
    {
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
        $method_name = 'unset' . ucfirst($name);
        if (in_array($method_name, get_class_methods($this))) {
            return $this->$method_name($name);
        }
        unset($this->data[$name]);
        return $this;
    }

    /**
     * Set id
     *
     * @param string $name
     * @param $id
     * @return Section
     * @throws Exception
     */
    protected function setId($name, $id)
    {
        $class = get_class($this);
        if (isset($this->id)) {
            unset(static::$instances[$class][$this->id]);
        }
        if (isset(static::$instances[$class][$id])) {
            // Element with this id already exists
            throw new Exception('Id already in use');
        }
        $this->data[$name] = $id;
        static::$instances[$class][$id] = $this;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function unsetId($name)
    {
        $class = get_class($this);
        unset(static::$instances[$class][$this->id]);
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
    public function append($content, $as = null)
    {
        if (is_null($as)) {
            $this->content[] = $content;
            return $this;
        } else {
            if (array_key_exists($as, $this->index)) {
                throw new Exception('Key already exists: ' . $as);
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
    public function prepend($content, $as = null)
    {
        if (is_null($as)) {
            array_unshift($this->content, $content);
            return $this;
        } else {
            if (array_key_exists($as, $this->index)) {
                throw new Exception('Key already exists: ' . $as);
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
     * @param null $position
     * @param string $as
     * @return $this
     * @throws Exception
     */
    public function insert($content, $position = null, $as = null)
    {
        if (!is_null($as)) {
            if (array_key_exists($as, $this->index)) {
                throw new Exception('Key already exists: ' . $as);
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
        return $this;
    }
    
    /**
     * Заменить контент, добавленный как $as
     *
     * @param mixed $content
     * @param string $as
     * @return $this
     */
    public function replace($content, $as = null)
    {
        if (is_null($as)) {
            $this->content[] = $content;
            return $this;
        } else {
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
    public function clear()
    {
        $this->content = array();
        $this->index = array();
    }
    
    /**
     * Найти по ключу
     *
     * @param string $as
     * @return mixed|null
     */
    public function find($as)
    {
        return array_key_exists($as, $this->index) ? $this->index[$as] : null;
    }
    
    /**
     * Существует ли фрагмент с таким ключом
     *
     * @param string $as
     * @return boolean
     */
    public function exists($as)
    {
        return array_key_exists($as, $this->index);
    }

    /**
     * Cloning object
     *
     * @param void
     */
    public function __clone()
    {
        unset($this->data['id']);
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
    public function current()
    {
        return $this->content[$this->key()];
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->content);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next()
    {
        next($this->content);
    }
/**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind()
    {
        reset($this->content);
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
     * assign element template
     *
     * @param string $name
     * @param string $path
     * @return Section
     * @throws Exception
     */
    protected function setTemplate($name, $path)
    {
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