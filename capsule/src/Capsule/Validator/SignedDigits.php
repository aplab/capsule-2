<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 23.11.2013 0:20:58 YEKT 2013                                             |
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
 * SignedDigits.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class SignedDigits extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
            INVALID_VALUE = 'invalid_value';
    
    public function __construct() {
        parent::__construct();
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be contain only first minus and digit characters, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be contain only first minus and digit characters, %value% given.');
    }
    
    public function isValid($value) {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        if (!preg_match('/^-?\\d+$/', $this->value)) {
            $this->message(self::INVALID_VALUE);
            return $this->isValid = false;
        }
        $this->isValid = true;
        return $this->isValid;
    }
}