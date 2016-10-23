<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 09.07.2013 23:10:21 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui;

use Capsule\Ui\Ui as i;
/**
 * Ui.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 * @property Section $section
 */
class Ui extends i
{
    /**
     * Getter
     * Возвращает секцию по id.
     * Этот метод переопределен для того, чтобы работать с Section из
     * своего Namespace а не из родительского класса
     *
     * @param string $name
     * @return Section
     */
    public function __get($name) {
        return Section::getElementById($name);
    }
}