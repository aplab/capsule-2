<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 21.10.2016
 * Time: 0:51
 */

namespace Capsule\Tools;


class ArrayTools
{
    public static function isNumericKeys(array $array)
    {
        return !sizeof(preg_grep('/^\\d+$/', array_keys($array), PREG_GREP_INVERT));
    }

    public static function isAssoc(array $array)
    {
        if (array() === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }
}