<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 22.01.2013 7:00:52 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\TabControl;

use Capsule\I18n\I18n;
/**
 * SysTabControl.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property array $items
 * @property string $instanceName
 */
class TabControl
{
    /**
     * Object instances
     *
     * @var array
     */
    private static $instances = array();
    
    /**
     * Internal data
     *
     * @var array
     */
    private $data = array(
        'items' => array()
    );
        
    /**
     * Constructor
     *
     * @param string $instance_name
     * @throws Exception
     * @return self
     */
    public function __construct($instance_name) {
        if (array_key_exists($instance_name, self::$instances)) {
            $msg = I18n::t('Instance name already exists: ') . $instance_name;
            throw new \RuntimeException($msg);
        }
        self::$instances[$instance_name] = $this;
        $this->data['instanceName'] = $instance_name;
    }
    
    /**
     * Add item
     *
     * @param Tab $item
     * @param string $as alias (unique)
     * @return self
     */
    public function add(Tab $item, $as = null) {
        if (is_null($as)) {
            $this->data['items'][] = $item;
        } else {
            $this->data['items'][$as] = $item;
        }
        return $this;
    }
    
    /**
     * @param void
     * @return \ArrayIterator
     */
    protected function getItems($name) {
        return new \ArrayIterator($this->data[$name]);
    }
    
    /**
     * @param void
     * @return \ArrayIterator
     */
    protected function setItems($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * @param void
     * @return string
     */
    protected function setInstanceName($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * Getter
     *
     * @param string $name
     * @throwsException
     * @return mixed
     */
    public function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            return $this->$setter($value, $name);
        }
        $this->data[$name] = $value;
        return $this;
    }
}