<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 11 мая 2015 г. 0:53:54 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module\Catalog;

use Capsule\Core\Singleton;
/**
 * Catalog.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Catalog extends Singleton
{
    const ATTRIBUTE_TOKEN_PREFIX = 'attr';
    
    /**
     * Привязка к namespace
     *
     * @param void
     * @return string
     */
    public static function ns() {
        return __NAMESPACE__;
    }
}