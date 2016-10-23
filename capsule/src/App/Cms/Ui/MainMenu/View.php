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

namespace App\Cms\Ui\MainMenu;

use App\Cms\Ui\Ui;
use Capsule\Ui\DropdownMenu\Menu;
use Capsule\Ui\DropdownMenu\Delimiter;
use Capsule\Ui\DropdownMenu\SubPunct;
use Capsule\I18n\I18n;
/**
 * View.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    private $menu;

    private $instanceName;
    
    public function __construct(Menu $menu) {
        $this->menu = $menu;
        $this->instanceName = $this->menu->getInstanceName();
        Ui::getInstance()->onload->append(
            'this.system_menu = new CapsuleCmsMainMenu(\'' . $this->menu->getInstanceName() . '\');');
    }

    public function __toString() {
        ob_start();
        include 'template.php';
        return ob_get_clean();
    }

    private function puncts() {
        $t = I18n::getInstance();
        $puncts = $this->menu->getPuncts();
        $ret = '';
        foreach ($puncts as $key => $punct) {
            $id = $this->instanceName . '-p' . $key;
            $ret.= '<div unselectable="on" class="punct" id="' . $id . '">'
                . $t($punct->getName())
                . $this->subPuncts($punct, $id)
                . '</div>';
        }
        return $ret;
    }

    private function subPuncts($punct, $id_prefix) {
        $t = I18n::getInstance();
        $sub_puncts = $punct->getSubPuncts();
        $ret = '';
        if (empty($sub_puncts)) {
            return $ret;
        }
        $ret.= '<div class="sub-place" id="' . $id_prefix . '-sp">';
        $ret.= '<div class="sub-container" id="' . $id_prefix . '-sc">';
        foreach($sub_puncts as $key => $sub_punct) {
            if ($sub_punct instanceof Delimiter) {
                $ret.= '<div class="delimiter"><div></div></div>';
                continue;
            }
            $get_parameters = $sub_punct->getGetParameters();
            $post_parameters = $sub_punct->getPostParameters();
            $has_parameters = sizeof($get_parameters) + sizeof($post_parameters);
            $target = '';
            $action = '';
            if ($has_parameters || $sub_punct->getDisabled() || $sub_punct->getAction() || (!$sub_punct->getUrl())) {
                $tag = 'div';
                $href = '';
                if ($sub_punct->getAction()) {
                    $action = ' onclick="' . $sub_punct->getAction() . '"';
                }
            } else {
                $tag = 'a';
                $href = ' href="' . $sub_punct->getUrl() . '"';
                if ($sub_punct->getTarget()) {
                    $target = ' target="' . $sub_punct->getTarget() . '"';
                }
            }
            $id = $id_prefix . '-s' . $key;
            $class = $sub_punct->getDisabled() ? 'sub-punct-disabled' : 'sub-punct';
            $ret.= '<div class="' . $class . '" id="' . $id . '">'
                . '<' . $tag . $href . $target . ' unselectable="on" class="sub-punct-label" id="' . $id . '-l"' . $action . '>'
                . $t($sub_punct->getName()) . '</' . $tag . '>';
            if (sizeof($sub_punct->getSubPuncts())) {
                $ret.= '<div class="sub-punct-arrow" id="' . $id . '-a"></div>';
            }
            if ($sub_punct->getIcon()) {
                $ret.= '<img id="' . $id . '-i" src="'
                        . $sub_punct->getIcon() . '">';
            }
            $ret.= $this->subPuncts($sub_punct, $id);
            if ($has_parameters) {
                $ret .= $this->form($sub_punct, $id . '-f');
            }
            $ret .= '</div>';
        }
        $ret.= '</div></div>';
        return $ret;
    }

    private function form(SubPunct $sub_punct, $id) {
        if ($sub_punct->getDisabled()) {
            return '';
        }
        $get_parameters = $sub_punct->getGetParameters();
        $post_parameters = $sub_punct->getPostParameters();
        $method = 'post';
        // $method = sizeof($post_parameters) ? 'post' : 'get';
        $ret = '<form method="' . $method . '" id="' . $id . '" action="'
                . $sub_punct->getAction();
        if (sizeof($get_parameters)) {
            $parameters = array();
            foreach ($get_parameters as $parameter) {
                $parameters[] = $parameter->getName() . '='
                        . $parameter->getValue();
            }
            $ret.= '?' . join('&amp;', $parameters);
        }
        $ret.= '">';
        if (sizeof($post_parameters)) {
            foreach ($post_parameters as $parameter) {
                $ret.= '<input type="hidden" name="' . $parameter->getName()
                    . '" value="' . $parameter->getValue() . '" />';
            }
        }
        $ret.= '</form>';
        return $ret;
    }
}