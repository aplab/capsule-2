<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 8:21:37 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Plugin\Storage;

use Capsule\Component\Config\Config;
use Capsule\Component\DataStorage\DataStorage;
use Capsule\Component\Json\Loader\Loader;
use Capsule\Component\Path\ComponentConfigPath;
use Capsule\Core\Fn;
use Capsule\Capsule;
use PHP\Exceptionizer\Exception;

/**
 * Storage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Storage
{
    /**
     * Default storage, where getInstance() called without param.
     *
     * @var string
     */
    const DEFAULT_STORAGE = 'default';

    /**
     * Driver namespace prefix
     *
     * @var string
     */
    const DRIVER_NS = 'Driver';

    /**
     * Storage instances
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Configuration data
     *
     * @var Config
     */
    protected static $config;

    /**
     * Concrete object properties
     *
     * @var array
     */
    private $data = array();

    /**
     * Returns configuration data
     * @return Config
     * @throws \Exception
     * @internal param $void
     */
    public static function config()
    {
        $name = __FUNCTION__;
        if (!self::$$name) {
            $class = get_called_class();
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                self::$$name = $storage->get($class);
            } else {
                $path = new ComponentConfigPath($class);
                $loader = new Loader($path, function ($json) {
                    return strtr($json, array(
                        '%{CAPSULE_SYSTEM_ROOT}' => Capsule::getInstance()->systemRoot,
                        '%{CAPSULE_DOCUMENT_ROOT}' => Capsule::getInstance()->documentRoot,
                    ));
                });
                $data = $loader->loadToArray();
                $$name = new Config($data);
                $storage->set($class, $$name);
                self::$$name = $$name;
            }
        }
        return self::$$name;
    }

    /**
     * Returns required or default instance
     *
     * @param string $instance_name
     * @return self
     */
    public static function getInstance($instance_name = null)
    {
        if (is_null($instance_name)) {
            $instance_name = self::DEFAULT_STORAGE;
        }
        if (!isset(self::$instances[$instance_name])) {
            self::$instances[$instance_name] = new self(self::config()->$instance_name);
        }
        return self::$instances[$instance_name];
    }

    /**
     * Возвращает имя хранилища
     *
     * @param self $storage
     * @throws Exception
     * @return string
     */
    public static function getInstanceName(self $storage)
    {
        foreach (self::$instances as $k => $v) {
            if ($storage === $v) return $k;
        }
        $msg = 'Unknown storage';
        throw new \Exception($msg);
    }

    /**
     * @param Config $config
     * @return self
     */
    private function __construct(Config $config)
    {
        $ns = Fn::get_namespace($this);
        $driver_classname = Fn::cc(self::DRIVER_NS . '/' . $config->driver, $this);
        $this->data['driver'] = new $driver_classname($config);
    }

    /**
     * Returns property value
     *
     * @param string
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }

    /**
     * Handler property value change.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $msg = get_class($this) . '::$' . $name . ' is read only';
        } else {
            $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        }
        throw new \Exception($msg);
    }

    /**
     * Disable cloning
     *
     * @param void
     * @return void
     */
    final public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    /**
     * Add file
     *
     * @param string $source_absolute_path
     * @param string $extension
     * @return string
     */
    public function addFile($source_absolute_path, $extension = null)
    {
        return $this->driver->addFile($source_absolute_path, $extension);
    }

    public function delFile($filename)
    {
        return $this->driver->delFile($filename);
    }
}