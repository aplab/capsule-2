<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 21:49:51 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Properties;

use Capsule\DataModel\Config\AbstractConfig;
use Capsule\DataModel\Exception;
use Capsule\Common\Filter;
use Capsule\Validator\Validator;

/**
 * Property.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Property extends AbstractConfig
{
    /**
     * Зарезервированное свойство
     *
     * @var string
     */
    const
        VALIDATOR = 'validator',
        COLUMN = 'column',
        FORM_ELEMENT = 'formElement';

    /**
     * @var string
     */
    const VALIDATOR_DEFAULT_NS = '\\Capsule\\Validator';

    /**
     * @param array $data
     * @throws Exception
     */
    public function __construct(array $data) {
        parent::__construct($data);
        if (array_key_exists(self::VALIDATOR, $this->data)) {
            $tmp = $this->data[self::VALIDATOR];
            $validator = self::initValidator($tmp);
            if ($validator instanceof Validator) {
                $this->data[self::VALIDATOR] = $validator;
            } else {
                $msg = 'Wrong validator definition';
                throw new Exception($msg);
            }
        }
        if (array_key_exists(self::COLUMN, $this->data)) {
            $this->initColumn($this->data[self::COLUMN]);
        }
        if (array_key_exists(self::FORM_ELEMENT, $this->data)) {
            $this->initFormElement($this->data[self::FORM_ELEMENT]);
        }
    }
    
    private function initColumn(array $data) {
        foreach($data as $key => $data_item) {
            if (is_array($data_item)) {
                $this->data[self::COLUMN][$key] = new Column($data_item);
            }
        }
    }
    
    private function initFormElement(array $data) {
        foreach($data as $key => $data_item) {
            if (is_array($data_item)) {
                $this->data[self::FORM_ELEMENT][$key] = new FormElement($data_item);
            }
        }
    }

    /**
     * Separate function validator initialize
     *
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private static function initValidator($data) {
        if (!is_array($data)) {
            return false;
        }
        if (!is_null(Filter::digit(join(array_keys($data)), null))) {
            // Несколько валидаторов в виде индексного массива
            $msg = 'not supported, maybe in next time';
            trigger_error($msg, E_USER_ERROR);
        }
        if (!isset($data['type'])) {
            $msg = 'Unknown validator type';
            throw new Exception($msg);
        }
        $type = $data['type'];
        unset($data['type']);
        if (false === strpos($type, '\\')) {
            $type = self::VALIDATOR_DEFAULT_NS . '\\' . $type;
        }
        $validator = new $type;
        foreach ($data as $property => $value) {
            $validator->$property = $value;
        }
        return $validator;
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        return __CLASS__;
    }
}