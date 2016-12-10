<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace Capsule\Core;

use Capsule\I18n\I18n;
use PHP\Exceptionizer\Exceptionizer;

/**
 * public static functions.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Fn
{
    /**
     * @param string $piece_1
     * @param string $piece_2
     * ...
     * @param string $piece_n
     *
     * @return string
     */
    public static function concat()
    {
        return join(func_get_args());
    }

    /**
     * @param string $separator
     * @param string $piece_1
     * @param string $piece_2
     * ...
     * @param string $piece_n
     */
    public static function concat_ws()
    {
        $args = func_get_args();
        return join(array_shift($args), $args);
    }

    /**
     * @param string $separator
     * @param string $piece_1
     * @param string $piece_2
     * ...
     * @param string $piece_n
     */
    public static function concat_ws_ne()
    {
        $args = func_get_args();
        $se = array_shift($args);
        return join($se, array_filter($args));
    }

    /**
     * @deprecated
     * @param string $path
     * @param number $level
     * @return string
     */
    public static function updir($path, $level = 1)
    {
        for ($i = 0; $i < $level; $i++) {
            $path = dirname($path);
        }
        return $path;
    }

    /**
     * standardize path
     *
     * @param string
     * @return string
     */
    public static function stpath($path)
    {
        return preg_replace('|/{2,}|', '/', str_replace('\\', '/', $path));
    }

    /**
     * Join only nonempty string
     *
     * @param string $glue
     * @param array $pieces
     */
    public static function join_ne($glue, array $pieces)
    {
        return join($glue, array_filter($pieces));
    }

    /**
     * Returns namespace without classname
     *
     * @param string|object $class
     * @return string
     */
    public static function get_namespace($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $tmp = explode('\\', $class);
        array_pop($tmp);
        return join('\\', $tmp);
    }

    /**
     * Returns namespace without classname
     *
     * @param string|object $class
     * @return string
     */
    public static function ns($class)
    {
        return self::get_namespace($class);
    }

    /**
     * Returns classname without namespace
     *
     * @param string|object $class
     * @return string
     */
    public static function get_classname($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $tmp = explode('\\', $class);
        return array_pop($tmp);
    }

    /**
     * Returns controller classname with namespace
     *
     * @param string|object $class
     * @return string
     */
    public static function get_controller_class($class)
    {
        if (is_object($class)) {
            $class = self::get_namespace($class);
        }
        $tmp = explode('\\', $class);
        $root = array_shift($tmp);
        array_unshift($tmp, $root, 'Controller');
        array_push($tmp, 'Controller');
        return join('\\', $tmp);
    }

    /**
     * Alias of Create classname
     *
     * @param
     * @param string|object $context
     * @return string
     */
    public static function cc($name, $context = null)
    {
        $name = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]/u', '\\', $name);
        $name = preg_replace('/\\\\{2,}/', '\\', $name);
        if (is_null($context)) {
            if (preg_match('/^\\\\/', $name)) {
                return $name;
            }
            return '\\' . $name;
        }
        if (preg_match('/^\\\\/', $name)) {
            return $name;
        }
        if (is_object($context)) {
            $context = self::get_namespace($context);
        }
        $context = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]/u', '\\', $context);
        $context = preg_replace('/\\\\{2,}/', '\\', $context);
        return preg_replace('/\\\\{2,}/', '\\', '\\' . self::concat_ws('\\', $context, $name));
    }

    /**
     * Create classname
     *
     * @param
     * @param string|object $context
     * @return string
     */
    public static function create_classname($name, $context = null)
    {
        return static::cc($name, $context);
    }

    /**
     * @param string $str
     * @return string
     */
    public static function strip_spaces($str)
    {
        $matches = array();
        preg_match_all('/[^\\r\\n]+/', $str, $matches);
        return join(array_map('trim', array_shift($matches)));
    }

    /**
     * Являетс ли массив списком с числовыми ключами
     * Первый параметр массив для проверки
     * Второй параметр значит что ключи должны представлять собой
     * натуральный ряд чисел начинающийся с нуля и следующий по порядку
     *
     * @param array
     * @return boolean
     */
    public static function is_list(array $array, $n0 = true)
    {
        if (empty($array)) {
            return true;
        }
        $keys = join(array_keys($array));
        if (!ctype_digit($keys)) {
            return false;
        }
        if (!$n0) {
            return true;
        }
        return $keys === join(range(0, sizeof($array) - 1));
    }

    /**
     * Возвращает true, если $key может быть ключом массива.
     * В противном случае возвращает false.
     *
     * @param string $key
     * @param bool|string $throw_exception
     * @return bool
     */
    public static function is_key($key, $throw_exception = true)
    {
        // Подавление ошибок при использовании @ работает очень медленно.
        $valid = @array($key => null);
        if (empty($valid)) {
            if ($throw_exception) {
                throw new \InvalidArgumentException(I18n::t('Wrong key'));
            }
            return false;
        }
        return true;
    }

    /**
     * Возвращает array, содержащие все значения из массива array1, которых нет
     * в array2
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function array_diff_assoc_recursive(array $array1, array $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }

    /**
     * Принимает вывод функции microtime(false)
     * Возвращает разницу
     *
     * @param string $start_microtime
     * @param string $end_microtime
     * @return string
     */
    public static function worktime($start_microtime, $end_microtime)
    {
        list($m, $t) = explode(' ', $start_microtime);
        $start = bcadd($m, $t, 6);
        list($m, $t) = explode(' ', $end_microtime);
        $end = bcadd($m, $t, 6);
        return bcsub($end, $start, 6);
    }

    /**
     * Возвращает массив элементов если $val является значением с точечной нотацией,
     * Или null если не является
     *
     * @param string $val
     * @return NULL|array:
     */
    public static function split_dot($val)
    {
        settype($val, 'string');
        $tmp = explode('.', $val);
        array_walk($tmp, function (&$v, $k) {
            $v = trim($v);
        });
        $tmp = array_filter($tmp);
        if (sizeof($tmp) < 2) {
            return null;
        }
        return $tmp;
    }
}