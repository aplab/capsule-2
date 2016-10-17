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

namespace Capsule\Config\Json;


class Loader
{
    public function __construct($path, \Closure $prefilter = null)
    {
        $json = file_get_contents($path);
    }
}