<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2013-2013                                                   |
// +---------------------------------------------------------------------------+
// | 15.04.2013 10:06:17 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Validator;

/**
 * IValidator.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
interface ValidatorInterface
{
    public function isValid($value);
    public function getClean();
}