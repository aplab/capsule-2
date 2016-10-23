<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2013-2013                                                   |
// +---------------------------------------------------------------------------+
// | 19.04.2013 16:14:07 YEKT 2013                                             |
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
 * Ipv4.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property boolean $privateRange = true Запрещает успешное прохождение
 *           проверки для следующих частных
 *           IPv4-диапазонов: 10.0.0.0/8, 172.16.0.0/12 и 192.168.0.0/16.
 * @property boolean $reservedRange = true Запрещает успешное прохождение
 *           проверки для следующих зарезервированных IPv4-диапазонов:
 *           0.0.0.0/8, 169.254.0.0/16, 192.0.2.0/24 и 224.0.0.0/4.
 *           Данный флаг не применяется к IPv6-адресам.
 */
class Ipv4 extends Validator
{
    const   INVALID_TYPE = 'invalid_type',
            INVALID_VALUE = 'invalid_value';
    
    /**
     * (non-PHPdoc)
     * @see \Capsule\ValidatorAbstract::configure()
     */
    protected function __construct() {
        parent::__construct();
        $this->privateRange = true;
        $this->reservedRange = true;
        $this->messageTemplates = array(
            self::INVALID_TYPE =>
                'The parameter %name% must be a string, %type% given.',
            self::INVALID_VALUE =>
                'The parameter %name% must be a string, %value% given.');
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
        $flag = FILTER_FLAG_IPV4;
        if (!$this->privateRange) {
            $flag = $flag | FILTER_FLAG_NO_PRIV_RANGE;
        }
        if (!$this->reservedRange) {
            $flag = $flag | FILTER_FLAG_NO_RES_RANGE;
        }
        $this->value = filter_var($this->value, FILTER_VALIDATE_IP, $flag);
        $this->isValid = $value ? true : false;
        return $this->isValid;
    }
}