<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 08.03.2014 3:32:18 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller;

use Capsule\I18n\I18n;
/**
 * Tree.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Tree extends ReferenceController
{
    protected $moduleClass = 'Capsule\\Unit\\Tree';
    
    protected function listItems() {
        $class = $this->moduleClass;
        $err = $class::repair();
        if (sizeof($err)) {
            $msg = 'Corrupted elements detected: ' . join(', ', $err);
            $this->ui->alert->append(I18n::_($msg));
        }
        parent::listItems();
    }
}