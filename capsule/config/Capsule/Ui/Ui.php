<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 09.07.2013 21:55:00 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui;

use Capsule\Core\Singleton;
use Capsule\Exception;
use Capsule\I18n\I18n;
/**
 * WebUi.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Ui extends Singleton
{
    /**
     * Getter
     * Возвращает секцию по id
     *
     * @param string $name
     * @return Section
     */
    public function __get($name) {
        return  Section::getElementById($name);
    }

    /**
     * Setter
     *
     * @param string $name
     * @param multitype $value
     * @return Section
     */
    public function __set($name, $value) {
        $msg = I18n::t('Object has no properties');
        throw new Exception($msg);
    }

    /**
     * The __invoke() method is called when a script tries to call an object as a function.
     * Подключает шаблон секции, которая передана в качестве параметра.
     *
     * @param Section $o
     * @return string;
     */
    public function __invoke(Section $o) {
        $template = $o->template;
        if ($template) {
            ob_start();
            include $template;
            return ob_get_clean();
        }
        return '';
    }
}