<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 3:31:03 YEKT 2013                                             |
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

use Capsule\Exception;
/**
 * Menu.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Menu
{
    private $instanceName;

    private $puncts = array();

    private static $instances = array();

    public function __construct($instance_name) {
        if (array_key_exists($instance_name, self::$instances)) {
            $msg = 'Instance name "' . $instance_name . '" already exists';
            throw new Exception($msg);
        }
        self::$instances[$instance_name] = $this;
        $this->instanceName = $instance_name;
    }

    public function addPunct(Punct $punct) {
        $this->puncts[] = $punct;
    }

    public function getPuncts() {
        return $this->puncts;
    }

    public function getInstanceName() {
        return $this->instanceName;
    }
}