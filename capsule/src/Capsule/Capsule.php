<?php
/**
 * Created by PhpStorm.
 * User: polyanin
 * Date: 16.10.2016
 * Time: 15:13
 */

namespace Capsule;


class Capsule implements \Serializable
{
    /**
     * @param void
     * @return void
     * @throws \BadFunctionCallException
     */
    public function serialize() {
        throw new \BadFunctionCallException('You cannot serialize this object.');
    }

    /**
     * @param void
     * @return void
     * @throws \BadFunctionCallException
     */
    public function unserialize($serialized) {
        throw new \BadFunctionCallException('You cannot unserialize this object.');
    }

    /**
     * Prevent cloning
     *
     * @throws \BadFunctionCallException
     * @param void
     * @return void
     */
    public function __clone() {
        throw new \BadFunctionCallException('Clone is not allowed.');
    }
}