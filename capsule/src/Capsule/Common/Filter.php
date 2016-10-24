<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 25.05.2013 21:37:49 YEKT 2013                                             |
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

/**
 * Filter.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Filter
{
    /**
     * Проверяет входной параметр на содержание только цифр (целое
     * неотрицательное число)
     *
     * Возвращает строку из цифр или $fail
     *
     * @param mixed $value
     * @return mixed|string
     */
    public static function digit($value, $fail = false) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        if (!preg_match('/^\\d+$/', $value)) {
            return $fail;
        }
        return $value;
    }

    public static function int($value, $fail = false) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        ltrim($value, '+');
        if (!preg_match('/^-?\\d+$/', $value)) {
            return $fail;
        }
        return $value;
    }
    
    /**
     * Возвращает идентификатор в виде строки или $fail
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function id($value, $fail = false) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        $value = ltrim($value, '0');
        return preg_match('/^\\d+$/', $value) ? $value : $fail;
    }

    final public static function intGtZero($value, $fail = false) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        $value = ltrim($value, '0');
        return preg_match('/^\\d+$/', $value) ? $value : $fail;
    }
    
    /**
     * Возвращает decimal или $fail
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function decimal($value, $fail = false) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        if (preg_match('/^(\\d+\\.\\d+|\\.\\d+|\\d+\\.|\\d+)$/', $value)) {
            return $value;
        }
        return $fail;
    }
    
    /**
     * Returns string or $fail
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function string($value, $fail = false) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        return $value;
    }
    
    /**
     * Alias of string
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function str($value, $fail = null) {
        return self::string($value, $fail);
    }
    
    /**
     * Returns string or $fail
     * applies the "trim" to string
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function strt($value, $fail = null, $charlist = null) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        return $charlist ? trim($value, $charlist) : trim($value);
    }
    
    /**
     * Returns string or $fail
     * applies the "trim" to string
     * replaces more of 1 space symbols to 1 space
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function strts($value, $fail = '', $charlist = null) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        $value = preg_replace('/\\s+/', ' ', $value);
        return $charlist ? trim($value, $charlist) : trim($value);
    }
    
    /**
     * Returns string or $fail
     * applies the "trim" to string
     * Returns $fail if empty string
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function strtn($value, $fail = null, $charlist = null) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        $string = $charlist ? trim($value, $charlist) : trim($value);
        return $string ?: $fail;
    }
    
    /**
     * Returns string or $fail
     * applies the "trim" to string
     * Returns $fail if empty string
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function strtnl($value, $fail = null, $charlist = null) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        $string = $charlist ? trim($value, $charlist) : trim($value);
        return String::toLowerCase($string) ?: $fail;
    }
    
    /**
     * Returns string or $fail
     * applies the "trim" to string
     * Returns $fail if empty string
     *
     * @param mixed $value
     * @param mixed $fail
     * @return mixed|string
     */
    final public static function strtnu($value, $fail = null, $charlist = null) {
        if (!is_scalar($value)) {
            return $fail;
        }
        settype($value, 'string');
        $string = $charlist ? trim($value, $charlist) : trim($value);
        return String::toUpperCase($string) ?: $fail;
    }
    
    public static function path($path, $backslash = false) {
        $ret = preg_replace('|/{2,}|', '/', str_replace('\\', '/', self::strtn($path)));
        return $backslash ? str_replace('/', '\\', $ret) : $ret;
    }
    
    final public static function cutXmlDeclaration($xml_fragment) {
        return trim(preg_replace('/^<\\?xml.*?\\?>/isu', '', trim($xml_fragment)));
    }
}