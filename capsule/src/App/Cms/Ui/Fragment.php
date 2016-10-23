<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 29.10.2013 0:24:04 YEKT 2013                                             |
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

use Capsule\Exception;
/**
 * Fragment.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Fragment
{
    private $path, $data;

    /**
     * Включить содержимое файла как фрагмент
     * $static определяет способ включения, как контент или как код
     *
     * @param string $path
     * @param boolean $static
     * @throws Exception
     */
    public function __construct($path, $static = false) {
        $this->path = $path;
        if (!file_exists($path)) {
            $msg = 'File not found: ' . $path;
            throw new Exception($msg);
        }
        if ($static) {
            $this->data = file_get_contents($path);
        } else {
            ob_start();
            include($path);
            $this->data = ob_get_clean();
        }
    }

    public function __toString() {
        return $this->data;
    }
}