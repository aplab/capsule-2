<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 11 мая 2015 г. 16:03:39 YEKT 2015                                              |
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

use Capsule\Unit\Nested\Item;

/**
 * AttributeSectionLink.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class AttributeSectionLink extends Item
{
    const DEFAULT_TAB_NAME = 'Attribute';
    
    const DEFAULT_SORT_ORDER = 10000000;
    
    protected function getTabName($name) {
        if (!array_key_exists($name, $this->data)) return self::DEFAULT_TAB_NAME;
        return $this->data[$name];
    }
    
    protected function issetTabName($name) {
        return true;
    }
    
    protected function getSortOrder($name) {
        if (!array_key_exists($name, $this->data)) return self::DEFAULT_SORT_ORDER;
        return $this->data[$name];
    }
    
    protected function issetSortOrder($name) {
        return true;
    }
}