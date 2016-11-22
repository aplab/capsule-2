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

namespace Capsule\Component\HttpRequest;


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
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}