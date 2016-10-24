<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 19.11.2013 0:55:07 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Common;

use Capsule\Exception;

/**
 * Path.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Path
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $tmp = array();
        $a = func_get_args();
        if (empty($a)) {
            $msg = 'Path cannot be empty';
            throw new Exception($msg);
        }
        array_walk_recursive($a, function ($v, $k) use (& $tmp) {
            $tmp[] = strval($v);
        });
        $tmp = join('/', $tmp);
        $tmp = normalize_path($tmp);
        $this->path = $tmp;
    }

    /**
     * Implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function toString()
    {
        return $this->path;
    }

    /**
     * Explicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @see global_functions
     */
    public function normalize()
    {
        $this->path = normalize_path($this->path);
        return $this->path;
    }

    /**
     * @see global_functions
     */
    public function absolutize()
    {
        $this->path = absolute_path($this->path);
        return $this->path;
    }

    /**
     * Содержит то что передано в параметре
     *
     * @param multitype
     * @return boolean
     */
    public function contain()
    {
        $param = new self(func_get_args());
        $param = $param->toArray();
        $path = $this->toArray();
        return sizeof(array_intersect_assoc($path, $param)) === sizeof($param);
    }

    /**
     * Содержит то что передано в параметре
     *
     * @param multitype
     * @return boolean
     */
    public function containedIn()
    {
        $param = new self(func_get_args());
        $param = $param->toArray();
        $path = $this->toArray();
        return sizeof(array_intersect_assoc($param, $path)) === sizeof($path);
    }

    /**
     * Explicit conversion to array
     *
     * @param void
     * @return array
     */
    public function toArray()
    {
        return explode('/', $this->path);
    }

    /**
     * @param void
     * @return boolean
     */
    public function isDir()
    {
        return is_dir($this->path);
    }

    /**
     * @param void
     * @return boolean
     */
    public function isFile()
    {
        return is_file($this->path);
    }

    /**
     * @param void
     * @return boolean
     */
    public function isLink()
    {
        return is_link($this->path);
    }

    /**
     * @param void
     * @return boolean
     */
    public function fileExists()
    {
        clearstatcache();
        return file_exists($this->path);
    }

    /**
     * clearstatcache() не требуется, функция unlink() очистит данный кэш
     * автоматически. http://ru2.php.net/manual/ru/function.clearstatcache.php
     *
     * @param void
     * @return boolean
     * @throws Exception
     */
    public function unlink()
    {
        if (!$this->fileExists()) {
            return true;
        }
        unlink($this->path);
        if ($this->fileExists()) {
            $msg = 'Unable to unlink: ' . $this->path;
            throw new Exception($msg);
        }
        return true;
    }

    /**
     * @param void
     * @return \Capsule\Common\Path
     */
    public function dirname()
    {
        return new self(dirname($this->path));
    }

    /**
     * Вычитает путь, переданный в параметре, из текущего
     *
     * @param void
     * @return \Capsule\Common\Path
     */
    public function substract()
    {
        $param = new self(func_get_args());
        if (!$this->contain($param)) {
            return new self($this);
        }
        return new self(array_diff_assoc($this->toArray(), $param->toArray()));
    }

    /**
     * Returns extension or null
     *
     * @param void
     * @return string
     */
    public function extension($lcase = null)
    {
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);
        return $extension ? ($lcase ? strtolower($extension) : $extension) : null;
    }
}
