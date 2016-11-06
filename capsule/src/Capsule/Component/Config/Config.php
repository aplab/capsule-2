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
 * Time: 22:29
 */

namespace Capsule\Component\Config;


use Capsule\Tools\ArrayTools;
use Iterator, Countable;
use Capsule\Exception;

/**
 * Config.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Config implements Iterator, Countable
{
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array();

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
     * Config constructor.
     * Если $extract_numeric_keys false, то массивы, у которых все ключи
     * являются числовыми, не будут преобразованы в объекты.
     *
     * @param array $data
     * @param bool $extract_numeric_keys
     */
    public function __construct(array $data = array(), $extract_numeric_keys = false)
    {
        foreach ($data as $property_name => $property_value) {
            if (is_array($property_value)) {
                if ($extract_numeric_keys) {
                    $this->data[$property_name] = new static($property_value);
                } else {
                    if (ArrayTools::isNumericKeys($property_value)) {
                        // не преобразуем в объект, оставляем массивом
                        $this->data[$property_name] = $this->extractArray($property_value);
                    } else {
                        $this->data[$property_name] = new static($property_value);
                    }
                }
            } else {
                $this->data[$property_name] = $property_value;
            }
        }
    }

    protected function extractArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (ArrayTools::isNumericKeys($value)) {
                    // не преобразуем в объект, оставляем массивом
                    $data[$key] = $this->extractArray($value);
                } else {
                    $data[$key] = new static($value);
                }
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
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
     * @throws \Exception
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Undefined property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }

    /**
     * Обработка установки значения свойства.
     *
     * @param string $name
     * @param mixed $value
     * @throws \Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        } else {
            $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        }
        throw new \Exception($msg);
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data) || property_exists($this, $name);
    }

    /**
     * implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            $this->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Return an associative array of the stored data.
     *
     * @param void
     * @return array
     */
    public function toArray()
    {
        $ret = array();
        foreach ($this->data as $key => $value) {
            if ($value instanceof static) {
                $ret[$key] = $value->toArray();
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }
}