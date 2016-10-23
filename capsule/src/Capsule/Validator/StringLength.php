<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 27.11.2013 23:21:13 YEKT 2013                                              |
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
 * StringLength.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property int $min
 * @property int $max
 */
class StringLength extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
        SHORTER_THAN = 'shorther_than',
        LONGER_THAN = 'longer_than';

    public function __construct()
    {
        parent::__construct();
        $this->min = 0;
        $this->max = null;
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a string, %type% given.',
            self::SHORTER_THAN =>
                'The length of the parameter %name% should be not less than %min% characters.',
            self::LONGER_THAN =>
                'The length of the parameter %name% should be not more than %max% characters.');
    }

    public function isValid($value)
    {
        parent::isValid($value);
        if (!is_scalar($this->value)) {
            $this->message(self::INVALID_TYPE);
            return $this->isValid = false;
        }
        $this->value = (string)$this->value;
        $length = String::length($this->value);
        if (!is_null($this->min)) {
            if ($length < $this->min) {
                $this->message(self::SHORTER_THAN);
                return $this->isValid = false;
            }
        }
        if (!is_null($this->max)) {
            if ($length > $this->max) {
                $this->message(self::LONGER_THAN);
                return $this->isValid = false;
            }
        }
        $this->isValid = true;
        return $this->isValid;
    }

    protected function setMin($value, $name)
    {
        if (is_null($value)) {
            $this->data[$name] = null;
            return $this;
        }
        if (!preg_match('/^[[:digit:]]+$/', $value)) {
            $msg = 'unsigned integer expected';
            throw new Exception($msg);
        }
        $this->data[$name] = (int)$value;
        return $this;
    }

    protected function setMax($value, $name)
    {
        if (is_null($value)) {
            $this->data[$name] = null;
            return $this;
        }
        if (!preg_match('/^[[:digit:]]+$/', $value)) {
            $msg = 'unsigned integer expected';
            throw new Exception($msg);
        }
        $this->data[$name] = (int)$value;
        return $this;
    }
}