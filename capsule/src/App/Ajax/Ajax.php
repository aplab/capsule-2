<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.07.2013 21:58:06 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Ajax;

use App\AbstractApp\App;
use Capsule\Capsule;
use Capsule\Core\Fn;
use App\Ajax\Controller\Controller;
use Capsule\User\Auth;
/**
 * Ajax.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 */
class Ajax extends App
{
    /**
     * Disable create objects directly.
     *
     * @param void
     * @return self
     */
    protected function __construct() {
        $this->data['action'] = null;
    }
    
    protected function getController($name) {
        if (!array_key_exists($name, $this->data)) {
            $var = $this->config->action;
            if (!isset($_REQUEST[$var])) {
                return null;
            }
            $tmp = $_REQUEST[$var];
            if (!is_scalar($tmp)) {
                return null;
            }
            if (!$tmp) {
                return null;
            }
            $controller = $this->config->controller;
            $controller = $controller->get($tmp);
            if (!$controller) {
                return null;
            }
            $controller_class = Fn::create_classname($controller, $this->config->controllerDefaultNs);
            try {
                $this->data['action'] = $tmp;
                $this->$name = new $controller_class;
            } catch (\Exception $e) {
                $this->data[$name] = null;
            }
        }
        return $this->data[$name];
    }
    
    /**
     * Обеспечивает контроль типов контроллера
     * @param unknown $name
     * @param Controller $value
     */
    protected function setController(Controller $value, $name) {
        $this->data[$name] = $value;
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\AbstractApp\App::run()
     */
    public function run() {
        if (!Auth::getInstance()->currentUser) return;
        $c = $this->controller;
        if ($c) $c->handle();
    }
}