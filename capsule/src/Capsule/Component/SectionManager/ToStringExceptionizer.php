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

namespace Capsule\Component\SectionManager;

/**
 * http://php.net/manual/ru/language.oop5.magic.php#107509
 *
 * Class ErrorHandler
 * @package Capsule\Component\SectionsManager
 * @author daan dot broekhof at gmail dot com
 */
class ToStringExceptionizer
{
    protected static $exception;

    public static function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
    {
        restore_error_handler();
        if (isset(self::$exception))
        {
            $exception = self::$exception;
            // Always unset '_toStringException', we don't want a straggler to be found
            // later if something came between the setting and the error
            self::$exception = null;
            throw $exception;
        }
        return false;
    }

    public static function throwException($exception)
    {
        // Should not occur with prescribed usage, but in case of recursion:
        // clean out exception, return a valid string, and weep
        if (isset(self::$exception)) {
            self::$exception = null;
            return '';
        }
        self::$exception = $exception;
        return null;
    }
}