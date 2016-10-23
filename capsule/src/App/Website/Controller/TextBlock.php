<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 06.06.2014 5:56:29 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Website\Controller;

use Capsule\Module\TextBlock as t;
use Capsule\Common\Path;
use Capsule\Common\TplVar;
/**
 * TextBlock.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class TextBlock extends UnitController
{
    public function handle() {
        $object = t::getElementById($this->unit->moduleId);
        if (!$object) {
            echo'object ' . t::class . '#' . $this->unit->moduleId . ' not found';
            return;
        }
        $template = new Path($this->tplpath, $this->unit->template);
        TplVar::getInstance()->o = $object;
        include $template;
    }
}