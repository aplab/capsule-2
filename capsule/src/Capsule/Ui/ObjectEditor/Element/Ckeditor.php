<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 07.04.2014 5:40:15 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\ObjectEditor\Element;

/**
 * Ckeditor.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Ckeditor extends Element
{
    /**
     * (non-PHPdoc)
     * @see SplObserver::update()
     */
    public function update(\SplSubject $group) {
        if (1 < sizeof($group)) {
            foreach ($group as $e) {
                if ($this === $e) {
                    $msg = 'Element ' . get_class($this) . ' requires a separate group. Check the configuration file.';
                    throw new \Exception($msg);
                }
            }
        }
        $group->ckeditor = false;
        foreach ($group as $e) {
            if ($e instanceof $this) {
                $group->ckeditor = true;
            }
        }
    }
}