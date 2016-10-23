<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 14.06.2014 7:59:03 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Ajax\Controller;

use Capsule\Controller\IController;
use App\Ajax\Ajax;
/**
 * Controller.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Controller implements IController
{
    protected $cmd;
    
    public function __construct() {
        $cmd = Ajax::getInstance()->action;
        $exclude = array(
            'handle',
            __FUNCTION__
        );
        $methods = array_diff(get_class_methods($this), $exclude);
        if (in_array($cmd, $methods)) {
            $this->cmd = $cmd;
        }
    }
    
    public function handle() {
        $cmd = $this->cmd;
        if ($cmd) {
            $this->$cmd();
        }
    }
}