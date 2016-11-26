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
 * Time: 0:41
 */

namespace App\Cms\Ui\DataModel\DataGrid;


use Capsule\Core\Fn;
use Capsule\DataModel\Config\Config;
use Capsule\DataModel\Config\Properties\Column;
use Capsule\DataModel\Config\Properties\Property;

/**
 * Class Col
 * @package App\Cms\Ui\DataModel\DataGrid
 */
class Col
{
    /**
     * Default cell type
     *
     * @var string
     */
    const DEFAULT_CELL_TYPE = 'Text';

    /**
     * Internal data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Col constructor.
     * @param Property $property
     * @param Config $config
     * @param Column $column
     */
    public function __construct(Property $property, Config $config, Column $column)
    {
        $this->data['property'] = $property;
        $this->data['config'] = $config;
        $this->data['column'] = $column;
        $this->data['name'] = $property->name;
        $this->data['type'] = $column->get('type');
        if (!$this->data['type']){
            $this->data['type'] = static::DEFAULT_CELL_TYPE;
        }
        $cell_ns = Fn::ns($this) . '\\Cell';
        $cell_class = Fn::cc($this->data['type'], $cell_ns);
        $this->data['cell'] = new $cell_class($this);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property';
            throw new \RuntimeException($msg);
        }
        $this->data[$name] = $value;
        return $this;
    }
}