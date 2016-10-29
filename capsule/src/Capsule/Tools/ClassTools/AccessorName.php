<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 29.10.2016
 * Time: 9:05
 */

namespace Capsule\Tools\ClassTools;

/**
 * Class Accessor
 * @package Capsule\Tools\ClassTools
 */
trait AccessorName
{
    /**
     * @param string $name
     * @param string $type
     * @return string|false
     */
    protected static function _accessor($name, $type)
    {
        static $methods = [];
        static $names = [];
        $class = get_called_class();
        if (!isset($names[$class][$name][$type])) {
            echo 'access';
            if (!isset($methods[$class])) {
                echo 'load';
                $methods[$class] = array_flip(get_class_methods($class));
            }
            $method = $type . ucfirst($name);
            if (isset($methods[$method])) {
                $names[$class][$name][$type] = $method;
            } else {
                $names[$class][$name][$type] = false;
            }
        }
        return $names[$class][$name][$type];
    }

    /**
     * @param $name
     * @return false|string
     */
    protected static function _getter($name)
    {
        return self::_accessor($name, 'get');
    }

    /**
     * @param $name
     * @return false|string
     */
    protected static function _setter($name)
    {
        return self::_accessor($name, 'set');
    }

    /**
     * @param $name
     * @return false|string
     */
    protected static function _issetter($name)
    {
        return self::_accessor($name, 'isset');
    }

    /**
     * @param $name
     * @return false|string
     */
    protected static function _unsetter($name)
    {
        return self::_accessor($name, 'unset');
    }
}