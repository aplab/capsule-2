<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */
/**
 * @package Capsule
 */

namespace Capsule\Component\DataStorage;

use Capsule\Component\Path;
use Capsule\Core\Singleton;
use Capsule\Capsule;

/**
 * DataStorage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DataStorage extends Singleton
{
    /**
     * Экземпляры производных классов
     *
     * @var array
     */
    protected static $registry = array();

    /**
     * Возвращает массив производных объектов для групповых операций
     *
     * @return array
     */
    public static function getInstances()
    {
        return self::$registry;
    }

    /**
     * Current class name
     *
     * @var string
     */
    protected $class;

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
     * DataStorage constructor.
     */
    protected function __construct()
    {
        self::$registry[] = $this;
        $this->initPath();
    }

    /**
     * @param void
     * @return void
     */
    protected function initPath()
    {
        $class = get_class($this);
        $path = new Path(
            Capsule::getInstance()->systemRoot,
            Capsule::DIR_CACHE,
            $class);
        $this->path = $path->toString();
    }

    /**
     * getter
     *
     * @param string $name
     * @return NULL|mixed
     */
    public function get($name)
    {
        $path = $this->buildPathByKey($name);
        return file_exists($path) ? unserialize(file_get_contents($path)) : null;
    }

    /**
     * setter
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     * @return self
     */
    public function set($name, $value)
    {
        $path = $this->buildPathByKey($name);
        if (!file_exists($path)) {
            $dir = dirname($path);
            if (!is_dir($dir)) {
                $dir = mkdir($dir, 0755, true);
                if (!$dir) {
                    $msg = 'Unable to create directory';
                    throw new Exception($msg);
                }
            }
        }
        if (false === file_put_contents($path, serialize($value), LOCK_EX)) {
            $msg = 'Unable to write file';
            throw new Exception($msg);
        }
        return $this;
    }

    /**
     * unset
     *
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function drop($name)
    {
        $path = $this->buildPathByKey($name);
        if (false === unlink($path)) {
            $msg = 'Unable to delete file';
            throw new Exception($msg);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function exists($name)
    {
        $path = $this->buildPathByKey($name);
        return file_exists($path) ? true : false;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function buildPathByKey($key)
    {
        if (!isset($this->keyCache[$key])) {
            $hash = md5($key);
            $this->keyCache[$key] = $this->path . '/' . join('/', array_slice(str_split($hash, 3), 0, 3)) . '/' . $hash;
        }
        return $this->keyCache[$key];
    }

    /**
     * Clear all storage data (files only)
     *
     * @param void
     * @return boolean
     */
    public function flush()
    {
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
    public function destroy()
    {
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
    protected function emptyDir($dir, $preserve_dirs = true)
    {
        $list = glob($dir . '/*');
        foreach ($list as $item) {
            if (is_dir($item)) {
                $this->emptyDir($item, $preserve_dirs);
                if (!$preserve_dirs) {
                    if (false === rmdir($item)) {
                        $msg = 'Unable to delete directory';
                        throw new Exception($msg);
                    }
                }
            } else {
                if (false === unlink($item)) {
                    $msg = 'Unable to flush storage';
                    throw new Exception($msg);
                }
            }
        }
        return true;
    }
}