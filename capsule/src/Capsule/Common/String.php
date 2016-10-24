<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:29:08 YEKT 2013                                             |
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
 * String.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class String
{
    /**
     * Внутренняя кодировка для mbstring
     *
     * @staticvar string
     */
    const ENCODING = 'UTF-8';
    
    /**
     * Флаг инициализации модуля
     *
     * @var bool;
     */
    private static $hasBeenInitialized;
    
    /**
     * Инициализирует модуль.
     * Должен быть вызван перед первым использованием.
     *
     * @param void
     * @return void
     */
    public static function initialize() {
        if (self::$hasBeenInitialized) {
            return;
        }
        if (extension_loaded('mbstring')) {
            if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
                $msg = 'String functions are overloaded by mbstring';
                throw new Exception($msg);
            }
            mb_internal_encoding(self::ENCODING);
            if (mb_internal_encoding() === self::ENCODING) {
                return;
            }
            $msg = 'Unable to set internal encoding.';
            throw new Exception($msg);
        } else {
            $msg = 'Mbstring extension required.';
            throw new Exception($msg);
        }
    }
    
    /**
     * strlen
     *
     * @param string
     * @return int
     */
    public static function strlen($string) {
        return mb_strlen($string);
    }
    
    /**
     * alias of strlen
     *
     * @param string
     * @return int
     */
    public static function length($string) {
        return self::strlen($string);
    }
    
    /**
     * substr
     *
     * @param string $string
     * @param int $start
     * @param int $length
     */
    public static function substr($string, $start, $length = null){
        if (is_null($length)) {
            return mb_substr($string, $start);
        }
        return mb_substr($string, $start, $length);
    }
    
    /**
     * alias of substr
     *
     * @param string $string
     * @param int $start
     * @param int $length
     */
    public static function substring($string, $start, $length = null) {
        return self::substr($string, $start, $length);
    }
    
    /**
     * отрезать до заданной длины
     *
     * @param string $string
     * @param int $length
     * @param boolean $save_original
     */
    public static function cut(&$string, $length, $save_original = false) {
        $copy = $string;
        $copy = self::substr($copy, 0, $length);
        if ($save_original) {
            return $copy;
        } else {
            $string = $copy;
            return $string;
        }
    }
    
    /**
     * Converts a string to an array.
     * string The input string.
     * split_length Maximum length of the chunk.
     *
     * @param string $string
     * @param int $split_length
     */
    public static function str_split_text($string, $split_length = 1) {
        $split_length = (int)Filter::digit($split_length); 
        if ($split_length < 1) {
            return false;
        }
        $length = self::length($string);
        if ($length <= $split_length) {
            return array(0 => $string);
        }
        $parts_number = ceil($length / $split_length);
        $ret = array();
        for ($i = 0; $i < $parts_number; $i++) {
            $ret[$i] = self::substring($string, $i * $split_length, $split_length);
        }
        return $ret;
    }
    
    /**
     * str_replace
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param int $count
     */
    public static function str_replace($search, $replace, $subject, $count = null) {
        if (is_null($count)) {
            return str_replace($search, $replace, $subject);
        }
        return str_replace($search, $replace, $subject, $count);
    }
    
    /**
     * alias of str_replace
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param int $count
     */
    public static function replace($search, $replace, $subject, $count = null) {
        return self::str_replace($search, $replace, $subject, $count);
    }
    
    /**
     * strtolower — Преобразует строку в нижний регистр
     *
     * @param string
     * @return string
     */
    public static function strtolower($string) {
        return mb_strtolower($string);
    }
    
    /**
     * strtoupper — Преобразует строку в верхний регистр
     *
     * @param string
     * @return string
     */
    public static function strtoupper($string) {
        return mb_strtoupper($string);
    }
    
    /**
     * alias of strtolower — Преобразует строку в нижний регистр
     *
     * @param string
     * @return string
     */
    public static function toLowerCase($string) {
        return self::strtolower($string);
    }
    
    /**
     * alias of strtoupper — Преобразует строку в верхний регистр
     *
     * @param string
     * @return string
     */
    public static function toUpperCase($string) {
        return self::strtoupper($string);
    }
    
    /**
     * str_ireplace — Case-insensitive version of str_replace().
     *
     * @param string $search
     * @param string $replace
     * @param string $string
     * @param int $count
     */
    public static function str_ireplace($search, $replace, $string, $count = null) {
        if (!is_array($search)) {
            $slen   = strlen($search);
            $lendif = strlen($replace) - $slen;
            if ( $slen == 0 ) {
                return $string;
            }
            $search  = self::strtolower($search);
            $search  = preg_quote($search, '/');
            $lstr    = self::strtolower($string);
            $i       = 0;
            $matched = 0;
            while (preg_match('/(.*)'.$search.'/Us',$lstr, $matches)) {
                if ($i === $count) {
                    break;
                }
                $mlen     = strlen($matches[0]);
                $lstr     = substr($lstr, $mlen);
                $string   = substr_replace($string, $replace, $matched+strlen($matches[1]), $slen);
                $matched += $mlen + $lendif;
                $i++;
            }
            return $string;
        } else {
            foreach (array_keys($search) as $k) {
                if (is_array($replace)) {
                    if (array_key_exists($k,$replace)) {
                        $string = self::ireplace($search[$k], $replace[$k], $string, $count);
                    } else {
                        $string = self::ireplace($search[$k], '', $string, $count);
                    }
                } else {
                    $string = self::ireplace($search[$k], $replace, $string, $count);
                }
            }
            return $string;
        }
    }
    
    /**
     * alias of str_ireplace — Case-insensitive version of str_replace().
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param int $count
     */
    public static function ireplace($search, $replace, $subject, $count = null) {
        return self::str_ireplace($search, $replace, $subject, $count);
    }
    
    /**
     * strpos — Возвращает позицию первого вхождения символа
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @return int
     */
    public static function strpos($string, $search, $offset = null){
        if (strlen($string) && strlen($search)) {
            if (is_null($offset)) {
                return mb_strpos($string, $search);
            } else {
                return mb_strpos($string, $search, $offset);
            }
        } else return false;
    }
    
    /**
     * stripos — Возвращает позицию первого вхождения подстроки без учета регистра
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @return int
     */
    public static function stripos($string ,$search, $offset = null) {
        if (strlen($string) && strlen($search)) {
            $string = self::strtolower($string);
            $search = self::strtolower($search);
            if (is_null($offset)) {
                return mb_strpos($string, $search);
            } else {
                return mb_strpos($string, $search, $offset);
            }
        } else return false;
    }
    
    /**
     * strrpos — Возвращает позицию последнего вхождения символа
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @return int
     */
    public static function strrpos($str, $search, $offset = null){
        if (is_null($offset)) {
            # Emulate behavior of strrpos rather than raising warning
            if (empty($str)) {
                return false;
            }
            return mb_strrpos($str, $search);
        } else {
            if (!is_int($offset)) {
                $msg = 'strrpos expects parameter 3 to be long';
                trigger_error($msg, E_USER_WARNING);
                return false;
            }
            $str = mb_substr($str, $offset);
            if (false !== ($pos = mb_strrpos($str, $search))) {
                return $pos + $offset;
            }
            return false;
        }
    }
    
    /**
     * str_split — Преобразует строку в массив
     *
     * @param string $string
     * @param int $split_length
     * @return array
     */
    public static function str_split($str, $split_length = null) {
        if (!preg_match('/^[0-9]+$/',$split_length) || $split_length < 1) {
            return false;
        }
        $len = self::strlen($str);
        if ($len <= $split_length) {
            return array($str);
        }
        preg_match_all('/.{'.$split_length.'}|[^\x00]{1,'.$split_length.'}$/us', $str, $ar);
        return $ar[0];
    }
    
    /**
     * strcasecmp — Сравнение строк без учета регистра, безопасное для данных в двоичной форме
     *
     * @param string $str1
     * @param string $str2
     * @return int
     */
    public static function strcasecmp($str1, $str2) {
        $str1 = self::strtolower($str1);
        $str2 = self::strtolower($str2);
        return strcmp($str1, $str2);
    }
    
    /**
     * strcspn — Возвращает длину участка в начале строки, не соответствующего маске
     *
     * @param string $string
     * @param string $mask
     * @param int $start
     * @param int $length
     * @return int
     */
    public static function strcspn($string, $mask, $start = null, $length = null) {
        if (empty($mask) || strlen($mask) == 0) {
            return null;
        }
        $mask = preg_replace('!([\\\\\\-\\]\\[/^])!','\\\${1}', $mask);
        if ($start !== null || $length !== null) {
            $string = self::substr($string, $start, $length);
        }
        preg_match('/^[^'.$mask.']+/u', $string, $matches);
        if (isset($matches[0])) {
            return self::strlen($matches[0]);
        }
        return 0;
    }
    
    /**
     * stristr — Регистро-независимый вариант функции strstr().
     *
     * @param string $haystack
     * @param string $needle
     * @return string
     */
    public static function stristr($haystack, $needle) {
        if (strlen($needle) == 0) {
            return $haystack;
        }
        $lstr = self::strtolower($haystack);
        $lsearch = self::strtolower($needle);
        preg_match('|^(.*)'.preg_quote($lsearch).'|Us',$lstr, $matches);
        if (count($matches) == 2) {
            return substr($haystack, strlen($matches[1]));
        }
        return false;
    }
    
    /**
     * strrev — Переворачивает строку
     *
     * @param string
     * @return string
     */
    public static function strrev($string) {
        preg_match_all('/./us', $string, $ar);
        return join('', array_reverse($ar[0]));
    }
    
    /**
     * strspn — Возвращает длину участка в начале строки, соответствующего маске
     *
     * @param string $string
     * @param string $mask
     * @param int $start
     * @param int $length
     * @return int
     */
    public static function strspn($str, $mask, $start = NULL, $length = NULL) {
        $mask = preg_replace('!([\\\\\\-\\]\\[/^])!','\\\${1}', $mask);
        if ( $start !== NULL || $length !== NULL ) {
            $str = self::substr($str, $start, $length);
        }
        preg_match('/^['.$mask.']+/u', $str, $matches);
        if ( isset($matches[0]) ) {
            return self::strlen($matches[0]);
        }
        return 0;
    }
    
    /**
     * substr_replace — Заменяет часть строки
     *
     * string $string
     * string $replacement
     * int $start
     * int $length
     */
    public static function substr_replace($str, $replacement, $start , $length = null) {
        preg_match_all('/./us', $str, $ar);
        preg_match_all('/./us', $replacement, $rar);
        if(is_null($length)) {
            $length = self::strlen($str);
        }
        array_splice($ar[0], $start, $length, $rar[0]);
        return join('', $ar[0]);
    }
    
    /**
     * ltrim — Удаляет пробелы из начала строки
     *
     * @param string $string
     * @param string $charlist
     * @return string
     */
    public static function ltrim($string, $charlist = null) {
        if (is_null($charlist)) {
            return ltrim($string);
        }
        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $charlist);
        return preg_replace('/^['.$charlist.']+/u', '', $string);
    }
    
    /**
     * rtrim — Удаляет пробелы из конца строки
     *
     * @param string $string
     * @param string $charlist
     * @return string
     */
    public static function rtrim($string, $charlist = null) {
        if (is_null($charlist)) {
            return rtrim($string);
        }
        //quote charlist for use in a characterclass
        $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $charlist);
        return preg_replace('/['.$charlist.']+$/u', '', $string);
    }
    
    /**
     * trim — Удаляет пробелы из начала и конца строки
     *
     * @param string $string
     * @param string $charlist
     * @return string
     */
    public static function trim($string, $charlist = null) {
        if (is_null($charlist)) {
            return trim($string);
        }
        return self::ltrim(self::rtrim($string, $charlist), $charlist);
    }
    
    /**
     * ucfirst — Преобразует первый символ строки в верхний регистр
     *
     * @param string $string
     * @return string
     */
    public static function ucfirst($string){
        switch (self::strlen($string) ) {
            case 0:
                return '';
                break;
            case 1:
                return self::strtoupper($string);
                break;
            default:
                preg_match('/^(.{1})(.*)$/us', $string, $matches);
                if (2 > count($matches)) {
                    $msg = 'Perhaps the string is not encoded in utf-8';
                    trigger_error($msg, E_USER_ERROR);
                }
                return self::strtoupper($matches[1]).$matches[2];
                break;
        }
    }
    
    /**
     * lcfirst — Преобразует первый символ строки в нижний регистр
     *
     * @param string $string
     * @return string
     */
    public static function lcfirst($string){
        switch (self::strlen($string)) {
            case 0:
                return '';
                break;
            case 1:
                return self::strtolower($string);
                break;
            default:
                preg_match('/^(.{1})(.*)$/us', $string, $matches);
                return self::strtolower($matches[1]).$matches[2];
                break;
        }
    }
    
    /**
     * ucwords — Преобразует в верхний регистр первый символ каждого слова в строке
     *
     * @param string $string
     * @return string
     */
    public static function ucwords($string) {
        // Note: [\x0c\x09\x0b\x0a\x0d\x20] matches;
        // form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns
        // This corresponds to the definition of a "word" defined at http://www.php.net/ucwords
        $pattern = '/(^|([\x0c\x09\x0b\x0a\x0d\x20]+))([^\x0c\x09\x0b\x0a\x0d\x20]{1})[^\x0c\x09\x0b\x0a\x0d\x20]*/u';
        return preg_replace_callback($pattern, array(__CLASS__, 'ucwords_callback'), $string);
    }
    
    /**
     * Callback function for preg_replace_callback call in utf8_ucwords
     * You don't need to call this yourself
     *
     * @param array of matches corresponding to a single word
     * @return string with first char of the word in uppercase
     */
    private static function ucwords_callback($matches) {
        $leadingws = $matches[2];
        $ucfirst   = self::strtoupper($matches[3]);
        $ucword    = self::substr_replace(ltrim($matches[0]), $ucfirst, 0, 1);
        return $leadingws.$ucword;
    }
    
    /**
     * lcwords — Преобразует в нижний регистр первый символ каждого слова в строке
     *
     * @param string $string
     * @return string
     */
    public static function lcwords($string) {
        // Note: [\x0c\x09\x0b\x0a\x0d\x20] matches;
        // form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns
        // This corresponds to the definition of a "word" defined at http://www.php.net/ucwords
        $pattern = '/(^|([\x0c\x09\x0b\x0a\x0d\x20]+))([^\x0c\x09\x0b\x0a\x0d\x20]{1})[^\x0c\x09\x0b\x0a\x0d\x20]*/u';
        return preg_replace_callback($pattern, array(__CLASS__, 'lcwords_callback'), $string);
    }
    
    /**
     * Callback function for preg_replace_callback call in utf8_lcwords
     * You don't need to call this yourself
     *
     * @param array of matches corresponding to a single word
     * @return string with first char of the word in lowercase
     */
    private static function lcwords_callback($matches) {
        $leadingws = $matches[2];
        $ucfirst   = self::strtolower($matches[3]);
        $ucword    = self::substr_replace(ltrim($matches[0]), $ucfirst, 0, 1);
        return $leadingws.$ucword;
    }
    
    /**
     * htmlspecialchars —  Преобразует специальные символы в HTML сущности
     *
     * @param string $string
     * @param int $quote_style
     * @param string $charset
     * @return string
     */
    public static function htmlspecialchars($string, $quote_style = null, $charset = null) {
        if (is_null($charset) && is_null($quote_style)) {
            return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        }
        if (is_null($charset)) {
            return htmlspecialchars($string, $quote_style, 'UTF-8');
        }
        return htmlspecialchars($string, $quote_style, $charset);
    }
    
    /**
     * wordwrap —  Выполняет перенос строки на данное количество символов
     * с использованием символа разрыва строки
     *
     * @param string $string
     * @param int $width
     * @param string $break
     * @param boolean $cut
     * @return string
     */
    public static function wordwrap($string, $width = null, $break = null, $cut = null) {
        if (is_null($cut)) {
            $cut = false;
        }
        if (is_null($break)) {
            $break = "\n";
        }
        if (is_null($width)) {
            $width = 75;
        }
        // We first need to explode on $break, not destroying existing (intended) breaks
        $lines = explode($break, $string);
        $new_lines = array(0 => '');
        $index = 0;
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            for ($i = 0, $size = sizeof($words); $i < $size; $i++) {
                $word = $words[$i];
                // If cut is true we need to cut the word if it is > width chars
                if ($cut && self::strlen($word) > $width) {
                    $words[$i] = self::substr($word, $width);
                    $word = self::substr($word, 0, $width);
                    $i--;
                }
                if (self::strlen($new_lines[$index] . $word) > $width) {
                    $new_lines[$index] = substr($new_lines[$index], 0, -1);
                    $index++;
                    $new_lines[$index] = '';
                }
                $new_lines[$index] .= $word . ' ';
            }
            $new_lines[$index] = substr($new_lines[$index], 0, -1);
            $index++;
            $new_lines[$index] = '';
        }
        unset($new_lines[$index]);
        return implode($break, $new_lines);
    }
    
    /**
     * ereg_replace — Replace regular expression
     *
     * @param string $pattern
     * @param string $replacement
     * @param string $string
     * @param string $option
     * @return string
     */
    public static function ereg_replace($pattern, $replacement, $string, $option = 'msr') {
        return mb_ereg_replace($pattern, $replacement, $string, $option);
    }
    
    /**
     * eregi_replace — Replace regular expression
     *
     * @param string $pattern
     * @param string $replacement
     * @param string $string
     * @param string $option
     * @return string
     */
    public static function eregi_replace($pattern, $replacement, $string, $option = 'msri') {
        return mb_eregi_replace($pattern, $replacement, $string, $option);
    }
    
    /**
     * ereg — Regular expression match
     *
     * @param string $pattern
     * @param string $string
     * @param array $regs
     * @return int
     */
    public static function ereg($pattern, $string, &$regs = null) {
        if (is_null($regs)) {
            return mb_ereg($pattern, $string);
        }
        return mb_ereg($pattern, $string, $regs);
    
    }
    
    /**
     * eregi — Case insensitive regular expression match
     *
     * @param string $pattern
     * @param string $string
     * @param array $regs
     * @return int
     */
    public static function eregi($pattern, $string, &$regs = null) {
        if (is_null($regs)) {
            return mb_eregi($pattern, $string);
        }
        return mb_eregi($pattern, $string, $regs);
    }
    
    /**
     * Длина строки в байтах
     *
     * @param string $string
     */
    public static function bytes($string) {
        return strlen($string);
    }
}