<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 25.02.2014 0:05:49 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Util;

use Capsule\Core\Singleton;
/**
 * Redirect.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Redirect extends Singleton
{
    protected $host;
    
    protected $uri;
    
    protected function __construct() {
        $this->host = $_SERVER['HTTP_HOST'];
        $this->uri = $_SERVER['REQUEST_URI'];
    }
    
    public function __invoke() {
        if (preg_match('/\\.$/i', $this->host)) {
            header("HTTP/1.0 404 Not Found", true, 404);
            header("HTTP/1.1 404 Not Found", true, 404);
            header("Status: 404 Not Found", true, 404);
            exit();
        }
        if (!(preg_match('/^www/i', $this->host))) {
            header('HTTP/1.1 301 Moved Permanently', true, 301);
            header('Location: http://www.' . preg_replace('|/{2,}|', '/', join('/', array($this->host, $this->uri))));
            exit();
        }
    }
}