<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 4:42:07 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui\Toolbar;

use Capsule\Ui\Toolbar\Delimiter;
use Capsule\Ui\Toolbar\Toolbar as t;
use Capsule\I18n\I18n;
use App\Cms\Ui\Ui;

/**
 * SysToolbarView.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    private $toolbar;
    
    private $instanceName;
    
    public function __construct(t $toolbar) {
        $this->toolbar = $toolbar;
        $this->instanceName = $this->toolbar->instanceName;
        $json = json_encode($toolbar, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        Ui::getInstance()->onload->append(
            'new CapsuleUiToolbar(' . $json . ');'
        );
    }
    
    public function __toString() {
        ob_start();
        include 'template.php';
        return ob_get_clean();
    }
    
    private function buttons() {
        $t = I18n::getInstance();
        $ret = '';
        if (!count($this->toolbar)) {
            return $ret;
        }
        foreach($this->toolbar as $key => $item) {
            if ($item instanceof Delimiter) {
                $ret.= '<div class="delimiter"><div></div></div>';
                continue;
            }
            $id = $this->instanceName . '-b' . $key;
            $class = $item->disabled ? ' disabled' : '';
            $ret.= '<div class="item' . $class . '" id="' . $id . '"><div class="bg">';
            if ($item->icon) {
                $ret.= '<div class="label i"><img src="'
                        . $item->icon . '" class="icon" />';
            } else {
                $ret.= '<div class="label">';
            }
            $ret.= $t($item->caption) . '</div></div>';
            $ret.= '</div>';
        }
        return $ret;
    }
}