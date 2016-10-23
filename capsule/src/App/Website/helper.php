<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 29.05.2014 6:00:16 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 * helper.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */

use App\Website\Website;

/**
 * Возвращает вывод области текущей страницы с именем $name
 * Глобальная функция.
 *
 * Может возникнуть ситуация, когда функция с таким именем будет уже где-нибудь
 * определена. Для решения проблемы можно переименовать эту функцию и её вызовы
 * в шаблонах страниц
 *
 * @param string $name
 * @return string
 */
function area($name) {
    $page = Website::getInstance()->page;
    $area = $page->area;
    $area = array_key_exists($name, $area) ? $area[$name] : null;
    if ($area) {
        return $area->toString();
    }
}