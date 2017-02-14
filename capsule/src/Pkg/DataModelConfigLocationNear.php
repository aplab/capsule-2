<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 14.02.2017
 * Time: 23:55
 */

namespace Pkg;


use Capsule\Component\Path\Path;

trait DataModelConfigLocationNear
{
    /**
     * Returns path to module config
     *
     * @return string
     */
    public static function _configLocation()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(static::$common[$c][$f])) {
            $r = new \ReflectionClass($c);
            static::$common[$c][$f] = new Path(
                dirname($r->getFileName()),
                $r->getShortName() . '.json');
        }
        return static::$common[$c][$f];
    }
}