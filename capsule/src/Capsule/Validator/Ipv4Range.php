<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 26.11.2013 0:06:27 YEKT 2013                                              |
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

use Capsule\Common\Filter;
/**
 * Ipv4Range.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Ipv4Range extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
            INVALID_VALUE = 'invalid_value',
            INVALID_ADDRESS = 'invalid_address',
            INVALID_MASK = 'invalid_mask';
    
    public function __construct() {
        parent::__construct();
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a string, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be valid IPv4 range, %value% given.',
            self::INVALID_ADDRESS =>
                '%name% Invalid address.',
            self::INVALID_MASK =>
                '%name% Invalid mask.');
    }
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\ValidatorAbstract::isValid()
     */
    public function isValid($value) {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        if (!$this->value) {
            $this->message(self::INVALID_VALUE);
            return $this->isValid = false;
        }
        $data = explode('/', $this->value);
        if (2 !== sizeof($data)) {
            $this->message(self::INVALID_VALUE);
            return $this->isValid = false;
        }
        $data = array_map('trim', $data);
        $validator = new Ipv4();
        $validator->setName($this->name);
        if ($validator->isValid(array_shift($data))) {
            $address = $validator->getClean();
        } else {
            $this->message(self::INVALID_ADDRESS);
            return $this->isValid = false;
        }
        $mask = array_shift($data);
        $mask = Filter::digit($mask, null);
        if (is_null($mask)) {
            $this->message(self::INVALID_MASK);
            return $this->isValid = false;
        }
        if ($mask > 32) {
            $this->message(self::INVALID_MASK);
            return $this->isValid = false;
        }
        $this->value = $address . '/' . $mask;
        $this->isValid = true;
        return $this->isValid;
    }
}