<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 16.06.2013 2:33:15 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Model;

use Capsule\DataModel\DataModel;
/**
 * CompositeKey.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class CompositeKey extends DataModel
{
    /**
     * Первичный ключ по нескольким свойствам
     *
     * @var array
     */
    protected static $key = array();
}