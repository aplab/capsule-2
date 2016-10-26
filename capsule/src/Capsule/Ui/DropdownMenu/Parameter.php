<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 3:20:48 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\DropdownMenu;

/**
 * Parameter.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Parameter
{
    const PARAMETER_GET  = 'get';
    const PARAMETER_POST = 'post';

    private $name, $value, $method;

    public function __construct($name, $value, $method = self::PARAMETER_POST) {
        settype($name, 'string');
        settype($value, 'string');
        settype($method, 'string');
        $this->name = $name;
        $this->value = $value;
        switch (strtolower($method)) {
            case self::PARAMETER_GET:
                $this->method = self::PARAMETER_GET;
                break;
            case self::PARAMETER_POST:
                $this->method = self::PARAMETER_POST;
                break;
            default:
                $this->method = self::PARAMETER_POST;
                break;
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    public function getMethod() {
        return $this->method;
    }

    public function isPost() {
        return self::PARAMETER_POST === $this->method;
    }

    public function isGet() {
        return self::PARAMETER_GET === $this->method;
    }
}