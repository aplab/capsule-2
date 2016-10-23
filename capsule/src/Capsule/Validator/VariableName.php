<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 26.11.2013 2:34:55 YEKT 2013                                              |
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
 * VariableName.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class VariableName extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
    INVALID_VALUE = 'invalid_value';
    
    public function __construct() {
        parent::__construct();
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a string, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be valid variable name '.
                'starts with a letter or underscore, followed by any '.
                'number of letters, numbers, or underscores, %value% given.');        
    }

    public function isValid($value) {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $this->value)) {
            $this->message(self::INVALID_VALUE);
            return $this->isValid = false;
        }
        $this->clean = strval($this->value);
        return $this->isValid = true;
    }
}