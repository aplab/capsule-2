<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 12 мая 2015 г. 0:35:17 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller\Catalog;

use App\Cms\Controller\Tree;
use Capsule\Module\Catalog\AttributeSectionLink;
/**
 * Section.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Section extends Tree
{
    protected $moduleClass = 'Capsule\\Module\\Catalog\\Section';

    /**
     * @param unknown $item
     * @param string $deep
     */
    protected function copyItem($item, $deep = false) {
        $copy = clone $item;
        $tmp = $this->createElement($copy);
        $new_item = $tmp->item;
        if (isset($new_item->id) && $new_item->id) {
            $new_item_id = $new_item->id;
            $links = AttributeSectionLink::getElementsByContainer($item->id);
            foreach ($links as $link) {
                $new_link = clone($link);
                $new_link->containerId = $new_item_id;
                $new_link->store();
            }
        }
        return $tmp;
    }
}