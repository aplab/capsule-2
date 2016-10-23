<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 25.11.2013 1:49:38 YEKT 2013                                             |
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
 * DbFieldName.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DbFieldName extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
            INVALID_VALUE = 'invalid_value';
    
    public function __construct() {
        parent::__construct();
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a string, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be valid database field name '.
                'starts with a letter in lower case, followed by any '.
                'number of letters in lower case, numbers, or underscores, '.
                'maximum length 64 characters, %value% given.');
    }
    
    public function isValid($value) {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        if (!preg_match('/^[a-z][a-z0-9_]{1,63}$/', $this->value)) {
            $this->message(self::INVALID_VALUE);
            return $this->isValid = false;
        }
        $this->value = strval($this->value);
        return $this->isValid = true;
    }
}