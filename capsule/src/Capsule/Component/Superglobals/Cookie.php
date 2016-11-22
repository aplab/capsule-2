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
 * Time: 23:50
 */

namespace Capsule\Component\Superglobals;


use Capsule\Component\Superglobals\DataSet;

/**
 * Class Cookie
 * @package Capsule\Component\Superglobals
 */
class Cookie extends DataSet
{
    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $_COOKIE);
    }
}