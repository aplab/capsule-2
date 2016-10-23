<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 28.03.2014 7:31:48 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Registry;

use Capsule\I18n\I18n;
/**
 * Immutable.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Immutable extends Registry
{
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        if (isset($this->$name)) {
            $msg = I18n::t('Cannot overwrite a property');
            throw new \RuntimeException($msg);
        }
        $this->registry[$name] = $value;
    }
    
    /**
     * Unset overloading
     *
     * @param string $name
     * @return void
     */
    public function __unset($name) {
        $msg = I18n::t('Cannot unset a property');
        throw new \RuntimeException($msg);
    }
}