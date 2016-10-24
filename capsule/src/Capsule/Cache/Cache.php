<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 27.05.2014 6:56:02 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Cache;

use Capsule\Component\Path\Path;
use Capsule\Core\Singleton;
use Capsule\Capsule;
/**
 * Cache.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Cache extends Singleton
{
    /**
     * Path to save data
     *
     * @var string
     */
    protected $path;
    
    /**
     * key cache
     *
     * @var array
     */
    protected $keyCache = array();
    
    /**
     * Keywords definition
     *
     * @var string
     */
    const EXPIRE = 'expire', VALUE = 'value';
    
    /**
     * Флаг содержит информацию о том, истек ли кеш, который был запрошен в 
     * последней операции get.
     * 
     * @var boolean
     */
    protected $expired = null;
    
    /**
     * Возвращает значение флага, содержащего информацию о том, истек ли кеш, 
     * который был запрошен в последней операции get.
     * 
     * @param void
     * @return boolean
     */
    public function expired() {
        return $this->expired ? true : false;
    }
    
    /**
     * @param void
     * @return self
     */
    protected function __construct() {
        $class = get_class($this);
        $path = new Path(
            Capsule::getInstance()->systemRoot,
            Capsule::DIR_CACHE,
            $class
        );
        $this->path = $path->toString();
    }
    
    /**
     * @param string $key
     * @return string
     */
    protected function keyToPath($key) {
        if (!isset($this->keyCache[$key])) {
            $hash = md5($key);
            $this->keyCache[$key] = $this->path . '/' .
                join('/', array_slice(str_split($hash, 3), 0, 3)) . '/' . $hash;
        }
        return $this->keyCache[$key];
    }
    
    /**
     * setter
     *
     * @param string $name
     * @param mixed $value
     * @param int $expire 0 never expired
     * @throws Exception
     * @return self
     */
    public function set($name, $value, $expire = null) {
        if (!$expire) {
            $expire = 0;
        } else {
            if (!ctype_digit((string)$expire)) {
                $msg = 'wrong expire value';
                throw new \Exception($msg);
            }
            $expire += time();
        }
        $path = $this->keyToPath($name);
        if (!file_exists($path)) {
            $dir = dirname($path);
            if (!is_dir($dir)) {
                $success = mkdir($dir, 0755, true);
                if (!is_dir($dir)) {
                    $msg = 'Unable to create directory: ' . $dir;
                    throw new \Exception($msg);
                }
            }
        }
        $data = array(
        	self::EXPIRE => $expire,
            self::VALUE => serialize($value)
        );
        if (false === file_put_contents($path, serialize($data), LOCK_EX)) {
            $msg = 'Unable to write file';
            throw new \Exception($msg);
        }
        return $this;
    }
    
    /**
     * getter
     *
     * @param string $name
     * @param boolean $ignore_expire
     * @return NULL|mixed
     */
    public function get($name, $ignore_expire = false) {
        $path = $this->keyToPath($name);
        if (!file_exists($path)) {
            return null;
        }
        $tmp = unserialize(file_get_contents($path));
        if ($tmp[self::EXPIRE] && (time() < $tmp[self::EXPIRE])) {
            $this->expired = true;
            // Кэш истек, убираем файл. В это время сами данные у нас в tmp
            if (false === unlink($path)) {
                $msg = 'Unable to delete file';
                throw new \Exception($msg);
            }
        } else {
            $this->expired = false;
        }
        if ($ignore_expire || !$tmp[self::EXPIRE]) {
            return unserialize($tmp[self::VALUE]);
        }
        if (time() < $tmp[self::EXPIRE]) {
            // Кэш ещё не истек
            return unserialize($tmp[self::VALUE]);
        }
        return null;
    }
    
    /**
     * unset
     *
     * @param string $name
     * @return self
     */
    public function drop($name) {
        $path = $this->keyToPath($name);
        if (false === unlink($path)) {
            $msg = 'Unable to delete file';
            throw new \Exception($msg);
        }
        return $this;
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function exists($name) {
        $path = $this->keyToPath($name);
        return file_exists($path) ? true : false;
    }
    
    /**
     * Clear all storage data (files only)
     *
     * @param void
     * @return boolean
     */
    public function flush() {
        if (!Capsule::$silent) {
            $msg = __METHOD__ . ' called';
            trigger_error($msg, E_USER_WARNING);
        }
        return $this->emptyDir($this->path);
    }
    
    /**
     * Clear all storage (dirs and files)
     *
     * @param void
     * @return boolean
     */
    public function destroy() {
        return $this->emptyDir($this->path, false);
    }
    
    /**
     * Clear directory content
     *
     * @param string $dir
     * @param boolean $preserve_dirs
     * @throws Exception
     * @return boolean
     */
    protected function emptyDir($dir, $preserve_dirs = true) {
        $list = glob($dir.'/*');
        foreach ($list as $item) {
            if (is_dir($item)) {
                $this->emptyDir($item, $preserve_dirs);
                if (!$preserve_dirs) {
                    if (false === rmdir($item)) {
                        $msg = 'Unable to delete directory';
                        throw new \Exception($msg);
                    }
                }
            } else {
                if (false === unlink($item)) {
                    $msg = 'Unable to flush cache';
                    throw new \Exception($msg);
                }
            }
        }
        return true;
    }
}