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
use Capsule\Component\Url\Path;
use Capsule\Core\Fn;
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
    protected function __construct()
    {
        $this->_init();
    }

    /**
     *
     */
    protected function _init()
    {
        $data = Path::getInstance()->data;
        $this->data['base'] = array_shift($data); // base (switch (select) app trigger)
        $this->data['mod'] = array_shift($data); // module (e.g. User, News etc) or mode (operation) (e.g. install, logout etc)
        $this->data['cmd'] = array_shift($data); // module command (e.g. module user, command: add, edit etc)
        $this->data['param'] = $data; // /user/edit/12/ 12 - parameter
    }

    /**
     * @throws \Throwable
     */
    public function run()
    {
        try {
            $mod = $this->mod;
            if (!Auth::getInstance()->user()) {
                return;
            }
            $config = $this->config->module;
            $controller_name = $config->get($mod);
            if ($controller_name) {
                $controller_name = Fn::cc($controller_name, $this->config->controllerDefaultNs);
                if ($controller_name) {
                    (new $controller_name)->handle();
                }
            }
        } catch (\Throwable $throwable) {
            if ($this->config->trace) {
                print json_encode([
                    'message' => $throwable->getMessage(),
                    'code' => $throwable->getCode(),
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                    'trace' => $throwable->getTrace()
                ]);
            } else {
                print json_encode([
                    'message' => $throwable->getMessage(),
                    'code' => $throwable->getCode()
                ]);
            }
        }
    }
}