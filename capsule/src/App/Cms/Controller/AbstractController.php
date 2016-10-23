<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 08.03.2014 3:36:08 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller;

use Capsule\Controller\AbstractController as a;
use App\Cms\Cms;

/**
 * AbstractController.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class AbstractController extends a
{
    protected function __construct() {
        $this->_init();
    }
    
    protected function _init() {
        $this->app = Cms::getInstance();
        $this->base = $this->app->base;
        $this->mod = $this->app->mod;
        $this->cmd = $this->app->cmd;
        $this->param = $this->app->param;
    }

    /**
     * @var Cms
     */
    protected $app;
    
    /**
     * Base
     *
     * @var string
     */
    protected $base;
    
    /**
     * mode or module
     *
     * @var string
     */
    protected $mod;
    
    /**
     * Команда
     *
     * @var string
     */
    protected $cmd;
    
    /**
     * Параметры
     *
     * @var array
     */
    protected $param;
}