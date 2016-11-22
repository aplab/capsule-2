<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 22.11.2016
 * Time: 8:43
 */

namespace Capsule\Component\Superglobals;


/**
 * Class DataSet
 * @package Capsule\Component\HttpRequest
 */
abstract class DataSet
{
    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    abstract public function get($name, $default = null);


    /**
     * @param $name
     * @return boolean
     */
    abstract public function __isset($name);

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @throws \Capsule\Component\Superglobals\Exception
     */
    public function __set($name, $value)
    {
        throw new Exception('Modification not allowed');
    }

    /**
     * @param $name
     * @param $default
     * @return bool|float|int|string
     */
    public function getScalar($name, $default = '')
    {
        if ($this->__isset($name)) {
            $value = $this->get($name);
            return is_scalar($value) ? $value : $default;
        }
        return $default;
    }

    /**
     * @param $name
     * @param $default
     * @return bool|float|int|string
     */
    public function getBoolean($name, $default = false)
    {
        if ($this->__isset($name)) {
            $value = $this->get($name);
            return !!$value;
        }
        return $default;
    }
}