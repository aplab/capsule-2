<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 14.06.2014 8:15:47 YEKT 2014                                              |
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

use App\Ajax\Controller\Controller;
use Capsule\Superglobals\Request;
use Capsule\Model\IdBased;
use Capsule\User\Auth;
/**
 * Nested.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Nested extends Controller
{
    protected function loadNested() {
        if (!Auth::getInstance()->currentUser) return;
        $r = Request::getInstance();
        $class = $r->gets('class');
        $id = $r->gets('id');
        if (!$class) {
            print json_encode(array());
            return;
        }
        if (!$id) {
            print json_encode(array());
            return;
        }
        if (!is_subclass_of($class, IdBased::class)) {
            print json_encode(array());
            return;
        }
        $list = $class::optionsDataListByContainer($id);
        print json_encode(array_values($list));
    }
}