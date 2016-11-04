<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 01.07.2013 23:54:49 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\AbstractApp;

use Capsule\Component\Config\Config;
use Capsule\Component\DataStorage\DataStorage;
use Capsule\Component\Json\Loader\Loader;
use Capsule\Component\Path\ComponentConfigPath;
use Capsule\Core\Singleton;
use Capsule\Exception;
use Capsule\Tools\ClassTools\AccessorName;

/**
 * App.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Config $config
 */
abstract class App extends Singleton
{
    use AccessorName;

    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array();
    
    /**
     * getter
     *
     * @param $name
     * @throws Exception
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
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }
    
    /**
     * setter
     *
     * @param $name
     * @param $value
     * @throws Exception
     * @return mixed
     */
    public function __set($name, $value)
    {
        $setter = static::_setter($name);
        if ($setter) {
            $this->$setter($value, $name);
            return $this;
        }
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
            throw new Exception($msg);
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getConfig($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $class = get_called_class();
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                $this->data[$name] = $storage->get($class);
            } else {
                $path = new ComponentConfigPath($class);
                $loader = new Loader($path);
                $data = $loader->loadToArray();
                $$name = new Config($data);
                $storage->set($class, $$name);
                $this->data[$name] = $$name;
            }
        }
        return $this->data[$name];
    }
    
    /**
     * @param void
     * @return void
     */
    abstract public function run();
    
    /**
     * Isset overloading
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }
}