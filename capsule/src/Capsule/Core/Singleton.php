<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 17.10.2016
 * Time: 23:09
 */

namespace Capsule\Core;

/**
 * Singleton.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Singleton implements \Serializable
{
    /**
     * @var array
     */
    private static $instances = array();

    /**
     * Singleton constructor.
     */
    protected function __construct() {}

    /**
     * Disable clone
     */
    public function __clone()
    {
        $msg = 'Clone is not allowed.';
        throw new \RuntimeException($msg);
    }

    /**
     * @param $class_name
     * @param $parameters
     * @return $this
     */
    protected static function getInstanceOf($class_name, $parameters)
    {
        if (!isset(self::$instances[$class_name])) {
            self::$instances[$class_name] = new $class_name($parameters);
        }
        return self::$instances[$class_name];
    }

    /**
     * @param null $class_name
     * @return bool
     */
    public static function instanceExists($class_name = null)
    {
        return array_key_exists($class_name ?: get_called_class(), self::$instances);
    }
    
    /**
     * @param void
     * @return static
     */
    public static function getInstance()
    {
        return self::getInstanceOf(get_called_class(), func_get_args());
    }
    
    /**
     * @param void
     * @return void
     * @throws \BadFunctionCallException
     */
    public function serialize()
    {
        throw new \BadFunctionCallException('You cannot serialize this object.');
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        throw new \BadFunctionCallException('You cannot unserialize this object.');
    }
}