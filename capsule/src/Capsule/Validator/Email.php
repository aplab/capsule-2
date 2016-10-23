<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 25.11.2013 2:37:40 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Validator;

use Capsule\Common\String;
/**
 * Email.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property boolean $allowEmpty = false Разрешить пустое значение
 * @property boolean $checkMX = true Проверять хост на DNS серверах
 */
class Email extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
            INVALID_VALUE = 'invalid_value',
            EMPTY_STRING = 'empty_string';
    
    public function __construct() {
        parent::__construct();
        $this->allowEmpty = false;
        $this->checkMX = true;
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a string, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be valid e-mail address, %value% given.',
            self::EMPTY_STRING =>
                'The parameter %name% must be nonempty string.');
    }
            
    public function isValid($value) {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        $this->value = String::trim($this->value);
        if (!$this->value) {
            if ($this->allowEmpty) {
                $this->value = '';
                return $this->isValid = true;
            }
            $this->message(self::EMPTY_STRING);
            return $this->isValid = false;
        }
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->message(self::INVALID_VALUE);
            return $this->isValid = false;
        }
        if ($this->checkMX) {
            $host = substr($this->value, strpos($this->value, '@') + 1);
            if (!$this->checkMX($host)) {
                $this->message(self::INVALID_VALUE);
                return $this->isValid = false;
            }
        }
        $this->value = strval($this->value);
        return $this->isValid = true;
    }
    
    /**
     * Проверяет MX-запись dns
     *
     * @param string $host
     * @return boolean
     */
    private function checkMX($host) {
        if (function_exists('checkdnsrr')) {
            return checkdnsrr($host, 'MX');
        }
        $msg = 'Could not retrieve DNS record information. set checkMX = false to prevent this warning';
        throw new Exception($msg);
    }
}