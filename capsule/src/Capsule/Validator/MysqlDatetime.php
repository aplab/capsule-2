<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 26.11.2013 2:34:29 YEKT 2013                                              |
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

/**
 * MysqlDatetime.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class MysqlDatetime extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
            INVALID_VALUE = 'invalid_value';
    
    public function __construct() {
        parent::__construct();
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a valid datetime string or null, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be a valid datetime string or null, %value% given.');
    }
    
    public function isValid($value) {
        parent::isValid($value);
        if (is_null($this->value)) {
            $this->isValid = true;
            $this->value = '0000-00-00 00:00:00';
            return $this->isValid;
        }
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        $this->value = (string) $this->value;
        if (!$value) {
            $this->value = '0000-00-00 00:00:00';
            $this->isValid = true;
            return $this->isValid;
        }
        if (!preg_match('/(\\d{4}).(\\d{2}).(\\d{2}).(\\d{2}).(\\d{2}).(\\d{2})/', $this->value, $matches)) {
            return $this->isValid = false;
        }
        $this->value = $matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' ' . $matches[4] . ':' . $matches[5] . ':' . $matches[6];
        $this->isValid = true;
        return $this->isValid;
    }
}