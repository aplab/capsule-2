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

namespace App\Cms\Ui\DataModel\DataGrid;


use Capsule\DataModel\Config\Config;
use Capsule\DataModel\Config\Properties\Column;
use Capsule\DataModel\DataModel;
use Capsule\Tools\ClassTools\AccessorName;

/**
 * Class DataGrid
 * @package App\Cms\Ui\DataModel\DataGrid
 * @property string $instanceName
 * @property Config $config
 * @property DataModel[] $items
 * @property Col[] $columns
 * @property int $itemsPerPage
 * @property array $itemsPerPageVariants
 * @property int $currentPage
 * @property int $pagesNumber
 * @property string $baseUrl
 */
class DataGrid
{
    use AccessorName;

    /**
     * Default column width
     */
    const DEFAULT_COLUMN_WIDTH = 60;

    /**
     * Default column sort order
     */
    const DEFAULT_COLUMN_SORT_ORDER = 0;

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
     * @param Config $config
     * @param \Traversable $items
     * @throws Exception
     */
    public function __construct($instance_name, Config $config, \Traversable $items)
    {
        if (array_key_exists($instance_name, static::$instances)) {
            throw new Exception('Instance already exists: ' . $instance_name);
        }
        $this->data['instanceName'] = $instance_name;
        static::$instances[$instance_name] = $this;
        $this->data['config'] = $config;
        $this->data['items'] = $items;
        $this->data['pagesNumber'] = 0;
        $this->data['currentPage'] = null;
        $this->data['itemsPerPageVariants'] = [];
        $this->data['itemsPerPage'] = [];
        $this->configure();
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
            return $this->$setter($value, $name);
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

    /**
     * Disable set instance name directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setInstanceName($value, $name)
    {
        $this->_readonlyException($name);
    }

    /**
     * Disable set config directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setConfig($value, $name)
    {
        $this->_readonlyException($name);
    }

    /**
     * Disable set columns directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setColumns($value, $name)
    {
        $this->_readonlyException($name);
    }

    /**
     * Disable set items directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setItems($value, $name)
    {
        $this->_readonlyException($name);
    }

    /**
     * Disable set some property directly
     *
     * @param $name
     */
    protected function _readonlyException($name)
    {
        $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     *
     */
    protected function configure()
    {
        $columns = [];
        foreach ($this->config->properties as $property_name => $property) {
            if (!isset($property->column)) {
                continue;
            }
            foreach ($property->column as $column_id => $column) {
                if (!($column instanceof Column)) {
                    continue;
                }
                if (!isset($column->width)) {
                    $column->width = static::DEFAULT_COLUMN_WIDTH;
                }
                if (!isset($column->order)) {
                    $column->order = static::DEFAULT_COLUMN_SORT_ORDER;
                }
                $column->property = $property_name;
                $columns[] = $column;
            }
        }
        usort($columns, function ($a, $b) {
            return $a->order <=> $b->order;
        });
        foreach ($columns as $column) {
            $this->data['columns'][] = new Col(
                $this->config->properties->{$column->property},
                $this->config,
                $column,
                $this
            );
        }
    }

    /**
     * @param $value
     * @param $name
     */
    protected function setCurrentPage($value, $name)
    {
        if (!preg_match('/^\\d+$/', $value)) {
            throw new \InvalidArgumentException('Wrong current page');
        }
        $this->data[$name] = $value;
    }

    /**
     * @param $value
     * @param $name
     */
    protected function setPagesNumber($value, $name)
    {
        if (!preg_match('/^\\d+$/', $value)) {
            throw new \InvalidArgumentException('Wrong pages number');
        }
        $this->data[$name] = $value;
    }

    /**
     * @param $value
     * @param $name
     */
    protected function setItemsPerPage($value, $name)
    {
        if (!preg_match('/^\\d+$/', $value)) {
            throw new \InvalidArgumentException('Wrong items per page');
        }
        $this->data[$name] = $value;
    }

    /**
     * @param $value
     * @param $name
     */
    protected function setBaseUrl($value, $name)
    {
        $this->data[$name] = (string)$value;
    }

    /**
     * @param array $value
     * @param $name
     */
    protected function setItemsPerPageVariants(array $value, $name)
    {
        array_filter($value, function($v) {
            if (!preg_match('/^\\d+$/', $v)) {
                throw new \InvalidArgumentException('Wrong items per page variant');
            }
        });
        $this->data[$name] = $value;
    }
}