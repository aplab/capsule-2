<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 08.04.2014 6:50:14 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Filter;
use Capsule\Component\Utf8String;

/**
 * Str.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Str
{
    /**
     * перевод строки на русском языке в транслит
     *
     * @param $string
     * @return string
     * @internal param string $st
     */
    public static function transliterate($string)
    {
        $string = strtr($string, array(
        'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e',
        'ж'=>'g', 'з'=>'z', 'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l',
        'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s',
        'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e',
        'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E',
        'Ж'=>'G', 'З'=>'Z', 'И'=>'I', 'Й'=>'Y', 'К'=>'K', 'Л'=>'L',
        'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R', 'С'=>'S',
        'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E'));
        $string = strtr($string, array(
                        'ё'=>"yo",   'х'=>"h", 'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh",
                        'щ'=>"shch", 'ъ'=>'',  'ь'=>'',   'ю'=>"yu", 'я'=>"ya",
                        'Ё'=>"Yo",   'Х'=>"H", 'Ц'=>"Ts", 'Ч'=>"Ch", 'Ш'=>"Sh",
                        'Щ'=>"Shch", 'Ъ'=>'',  'Ь'=>'',   'Ю'=>"Yu", 'Я'=>"Ya"));
        return $string;
    }

    /**
     * перевод строки на русском языке в транслит для url
     *
     * @param $string
     * @param bool $hyphen
     * @param bool $to_lower_case
     * @return string
     */
    public static function transliterateUrl($string, $hyphen = false, $to_lower_case = true)
    {
        $delimiter = $hyphen ? '-' : '_';
        $string = strtr($string, array(
        'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e',
        'ж'=>'g', 'з'=>'z', 'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l',
        'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s',
        'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e',
        'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E',
        'Ж'=>'G', 'З'=>'Z', 'И'=>'I', 'Й'=>'Y', 'К'=>'K', 'Л'=>'L',
        'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R', 'С'=>'S',
        'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E'));
        $string = strtr($string, array(
                        'ё'=>"yo",   'х'=>"h", 'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh",
                        'щ'=>"shch", 'ъ'=>'',  'ь'=>'',   'ю'=>"yu", 'я'=>"ya",
                        'Ё'=>"Yo",   'Х'=>"H", 'Ц'=>"Ts", 'Ч'=>"Ch", 'Ш'=>"Sh",
                        'Щ'=>"Shch", 'Ъ'=>'',  'Ь'=>'',   'Ю'=>"Yu", 'Я'=>"Ya"));
        $string = preg_replace('/[^a-zA-Z0-9]+/', $delimiter, $string);
        $string = preg_replace('/' . $delimiter . '{2}/', $delimiter, $string);
        $string = preg_replace('/^' . $delimiter . '+/', '', $string);
        $string = preg_replace('/' . $delimiter . '+$/', '', $string);
        if ($to_lower_case) {
            return Utf8String::toLowerCase($string);
        }
        return $string;
    }
}