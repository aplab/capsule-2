<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 22.05.2014 7:32:07 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Website\Structure;

use Capsule\Core\Singleton;
use Capsule\DataStruct\Loader;
use Capsule\Common\Path;
use Capsule\Capsule;
use Capsule\Core\Fn;
/**
 * Structure.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Structure extends Singleton
{
    /**
     * Pages
     *
     * @var array
     */
    protected $pages;
    
    /**
     * Storage
     *
     * @var Storage
     */
    protected $storage;
    
    /**
     * Disable create object directly
     *
     * @param void
     * @return self
     */
    protected function __construct() {
        $this->storage = Storage::getInstance();
        $this->_init();
    }
    
    /**
     * Getter
     *
     * @param string $path
     * @return Page
     */
    public function get($path) {
        if (array_key_exists($path, $this->pages)) {
            if (!($this->pages[$path] instanceof Page)) {
                $this->pages[$path] = $this->_page($path, $this->pages[$path]);
            }
            return $this->pages[$path];
        }
        return null;
    }
    
    /**
     * @param string $path
     * @param array $data
     * @return Page
     */
    protected function _page($path, array $data) {
        $key = __METHOD__ . $path;
        $page = $this->storage->get($key);
        if (!($page instanceof Page)) {
            $array = array(Element::ID => $path);
            if (array_key_exists(Element::ID, $data)) {
                $msg = 'Cannot redeclare page::id. Check configuration. Remove id definition from configuration.';
                throw new \Exception($msg);
            }
            $page = Page::createElement(array_replace($array, $data, $array));
            $this->storage->set($key, $page);
        }
        return $page;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param unknown $value
     * @throws \Exception
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . get_class($this) . '::$' . $name;
            throw new \Exception($msg);
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
    
    /**
     * Init data
     *
     * @param void
     * @return void
     */
    protected function _init() {
        $key = __METHOD__;
        $data = $this->storage->get($key);
        if (!is_array($data)) {
            $tmp = $this->_load();
            array_walk($tmp, function($v, $k) use (& $data) {
                $data[self::normalizePath($k)] = $v;
            });
            $this->storage->set($key, $data);
        }
        $this->pages = $data;
    }
    
    /**
     * Load data from configuration file
     *
     * @param void
     * @return array
     */
    protected function _load() {
        $class = get_class($this);
        $path = new Path(Capsule::getInstance()->cfg, $class . '.json');
        $loader = new Loader();
        return $loader->loadJson($path);
    }
    
    /**
     * Возвращает список путей страниц
     *
     * @param void
     * @return array
     */
    public function getRoutesList() {
        return array_keys($this->pages);
    }
    
    public static function normalizePath($path) {
        return preg_replace('|/{2,}|', '/', '/' . trim(trim($path), '/') . '/');
    }
}