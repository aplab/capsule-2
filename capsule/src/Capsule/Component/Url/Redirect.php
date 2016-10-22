<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 28.04.2014 6:54:09 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Url;

use Capsule\Superglobals\Server;
/**
 * Redirect.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Redirect
{
    public static function go($url)
    {
        $server = Server::getInstance();
        $file = null;
        $line = null;
        if (headers_sent($file, $line)) {
            $msg = 'Headers already sent in ' . $file . ' line ' . $line;
            throw new \RuntimeException($msg);
        }
        /**
         * TODO Сделать url абсолютным
         */
        header ('Location: ' . $url);
    }
}