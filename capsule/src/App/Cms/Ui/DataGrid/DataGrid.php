<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 24.11.2016
 * Time: 0:15
 */

namespace App\Cms\Ui\DataGrid;


use Capsule\Tools\ClassTools\AccessorName;

class DataGrid
{
    use AccessorName;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * @param $instance_name
     * @return DataGrid|null
     */
    public function getInstance($instance_name)
    {
        return array_key_exists($instance_name, static::$instances) ? static::$instances[$instance_name] : null;
    }

    /**
     * DataGrid constructor.
     * @param $instance_name
     * @param array $columns
     * @param \Traversable $items
     * @throws Exception
     */
    public function __construct($instance_name, array $columns, \Traversable $items)
    {
        if (array_key_exists($instance_name, static::$instances)) {
            throw new Exception('Instance already exists: ' . $instance_name);
        }
        static::$instances[$instance_name] = $this;
        array_filter($columns, function(Col $v) {});
        $this->data['items'] = $items;
        $this->data['columns'] = $columns;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $setter = static::_setter($name);
        if ($setter) {
            return $this->$setter($name, $value);
        }
        echo 'setter ';
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = static::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
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
}