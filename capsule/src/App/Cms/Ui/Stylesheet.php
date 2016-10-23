<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 24.10.2013 1:10:26 YEKT 2013                                             |
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

/**
 * Stylesheet.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Stylesheet
{
    private $href, $media;

    public function __construct($href, $media = 'all') {
        $this->href = $href;
        $this->media = $media;
    }

    public function __toString() {
        return'<link rel="stylesheet" href="' . $this->href . '" media="' .
            $this->media . '" />';
    }
}