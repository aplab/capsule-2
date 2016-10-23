<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 01.05.2014 7:07:15 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\DataGrid;

use Capsule\DataModel\Config\Config;
use Capsule\DataModel\Config\Properties\Column;
use Capsule\Core\Fn;

/**
 * Col.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
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
     * @var unknown
     */
    private $data = array();

    /**
     * Constructor set up required properties
     *
     * @param string $property_name
     * @param Config $config
     * @param c $column
     */
    public function __construct($property_name, Config $config, Column $column)
    {
        if (!isset($config->properties)) {
            $msg = 'Config has no properties';
            throw new \InvalidArgumentException($msg);
        }
        if (!isset($config->properties->$property_name)) {
            $msg = 'Unknown property';
            throw new \InvalidArgumentException($msg);
        }
        foreach ($column as $property => $value) {
            $this->data[$property] = $value;
        }
        $this->data['propertyName'] = $property_name;
        $this->data['config'] = $config;
        $this->data['column'] = $column;
        $this->data['property'] = $config->properties->$property_name;
        if (!isset($this->data['type'])) {
            $this->data['type'] = self::DEFAULT_CELL_TYPE;
        }
        $cell_ns = Fn::get_namespace($this) . '\\Cell';
        $cell_class = Fn::create_classname($this->type, $cell_ns);
        $this->data['cell'] = new $cell_class($this);
    }

    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

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