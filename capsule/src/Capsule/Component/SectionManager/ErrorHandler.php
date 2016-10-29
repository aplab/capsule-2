<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 28.10.2016
 * Time: 7:35
 */

namespace Capsule\Component\SectionsManager;

/**
 * http://php.net/manual/ru/language.oop5.magic.php#107509
 *
 * Class ErrorHandler
 * @package Capsule\Component\SectionsManager
 * @author daan dot broekhof at gmail dot com
 */
class ErrorHandler
{
    protected static $_toStringException;

    public static function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
    {
        if (isset(self::$_toStringException))
        {
            $exception = self::$_toStringException;
            // Always unset '_toStringException', we don't want a straggler to be found
            // later if something came between the setting and the error
            self::$_toStringException = null;
            if (preg_match('~^Method .*::__toString\(\) must return a string value$~', $errorMessage)) {
                throw $exception;
            }
        }
        return false;
    }

    public static function throwToStringException($exception)
    {
        // Should not occur with prescribed usage, but in case of recursion:
        // clean out exception, return a valid string, and weep
        if (isset(self::$_toStringException))
        {
            self::$_toStringException = null;
            return '';
        }

        self::$_toStringException = $exception;

        return null;
    }
}