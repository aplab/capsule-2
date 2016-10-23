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

namespace App\Cms\Ui\TabControl;

use Capsule\Ui\TabControl\TabControl as t;
use App\Cms\Ui\Script;
use App\Cms\Ui\Stylesheet;
use App\Cms\Cms;
use App\Cms\Ui\Ui;
use Capsule\I18n\I18n;
/**
 * View.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    private $tabControl;
    
    private $instanceName;
    
    private static $assetsIncluded;
    
    public function __construct(t $tab_control) {
        $this->tabControl = $tab_control;
        $this->instanceName = $this->tabControl->instanceName;
        if (!self::$assetsIncluded) {
            Ui::getInstance()->css->append(
                new Stylesheet(Cms::getInstance()->config->ui->tabControl->css)
            );
            Ui::getInstance()->js->append(
                new Script(Cms::getInstance()->config->ui->tabControl->js)
            );
        }
        $data = array(
            'instanceName' => $this->instanceName,
            'items'        => array()
        );
        $items = $this->tabControl->items;
        foreach($items as $key => $item) {
            $data['items'][] = $item->getData();
        }
        Ui::getInstance()->onload->append(
            'new CapsuleUiTabControl(' . json_encode(
                $data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE
            ) . ');'
        );
    }
    
    public function __toString() {
        ob_start();
        include 'template.php';
        return ob_get_clean();
    }
    
    private function tabs() {
        $items = $this->tabControl->items;
        $ret = '';
        if (empty($items)) {
            return $ret;
        }
        foreach($items as $key => $item) {
            $id = $this->instanceName . '-t' . $key;
            $ret.= '<div class="tab" id="' . $id . '">';
            $ret.= '<div unselectable="on" class="label"><div class="left-side"></div><div class="text">';
            $ret.= I18n::_($item->name) . '</div><div class="right-side"></div></div></div>';
        }
        return $ret;
    }
}