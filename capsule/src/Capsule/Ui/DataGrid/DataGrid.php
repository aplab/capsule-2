<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 20.01.2014 20:42:24 YEKT 2014                                              |
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
use Capsule\DataModel\DataModel;
use Capsule\Capsule;
use Capsule\DataModel\Config\Properties\Column;
use Capsule\I18n\I18n;
use Capsule\Ui\DataGrid\Cell\Checkbox;

/**
 * DataGrid.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $instanceName
 * @property int $defaultWidth
 * @property int $defaultOrder
 * @property array $items
 * @property array $columns
 * @property Config $config
 */
class DataGrid
{
    /**
     * Internal data
     *
     * @var array
     */
    private $data = array(
        'defaultWidth' => 60,
        'items' => array(),
        'columns' => array()
    );

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * Disable set config directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setConfig($value, $name)
    {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set columns directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setColumns($value, $name)
    {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set items directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setItems($value, $name)
    {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set instance name directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setInstanceName($value, $name)
    {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Массив объектов для вывода.
     * Объекты наследники Capsule\DataModel
     *
     * @param string $instance_name
     * @param array $items
     */
    public function __construct($instance_name, array $items = array())
    {
        $this->data['instanceName'] = $instance_name;
        if (empty($items)) {
            return;
        }
        $item = current($items);
        $this->data['class'] = get_class($item);
        $this->data['config'] = $item::config();
        $this->data['items'] = array_filter($items, function ($o) {
            return $o instanceof DataModel;
        });
        $this->configure();
    }

    /**
     * Конфигурация свойств для вывода колонок
     *
     * @param void
     * @return void
     */
    protected function configure()
    {
        $properties = $this->config->properties;
        $tmp = array();
        $has_checkbox = false;
        foreach ($properties as $property_name => $property) {
            if (!isset($property->column)) {
                continue;
            }
            foreach ($property->column as $column) {
                if ($column instanceof Column) {
                    if ($column instanceof Checkbox) {
                        if ($has_checkbox) {
                            $msg = 'Allowed only one column of type "Checkbox"';
                            throw new \RuntimeException($msg);
                        } else {
                            $has_checkbox = true;
                        }
                    }
                    if (!isset($column->width)) {
                        $column->width = $this->defaultWidth;
                    }
                    if (!isset($column->order)) {
                        $column->order = $this->defaultOrder;
                    }
                    $column->propertyName = $property_name;
                    $tmp[] = $column;
                }
            }
        }
        usort($tmp, function ($a, $b) {
            if ($a->order == $b->order) {
                return 0;
            }
            return ($a->order < $b->order) ? -1 : 1;
        });
        if (sizeof($this->items)) {
            // Отбираем только реально загруженные свойства объекта (по первому объекту из списка)
            $item = array_values($this->items)[0];
            foreach ($tmp as $column) {
                if (isset($item->{$column->propertyName})) {
                    $this->data['columns'][] = new Col($column->propertyName, $this->config, $column);
                }
            }
        }
    }
}