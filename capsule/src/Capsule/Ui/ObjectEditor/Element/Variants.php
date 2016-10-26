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

use Capsule\DataModel\DataModel;
use Capsule\DataModel\Config\Properties\FormElement;
use Capsule\User\Env;
use Capsule\I18n\I18n;
/**
 * Variants.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Variants extends Element
{
    public function __construct(DataModel $object, $name, $settings) {
        parent::__construct($object, $name, $settings);
        $this->data['options'] = array();
        if (isset($settings->variants)) {
            foreach ($settings->variants as $value => $text) {
                $this->data['options'][$value] = array('text' => I18n::_($text));
            }
        }
        $has_selected = false;
        if ($this->hasValue) {
            if (isset($this->options[$this->value])) {
                $option = $this->data['options'][$this->value]['selected'] = true;
                $has_selected = true;
            }
        }
        if (!$has_selected) {
            if ($settings instanceof FormElement) {
                $default = $settings->default;
                #$default = Env::getInstance()->get($default);
                if (isset($this->options[$default])) {
                    $option = $this->data['options'][$default]['selected'] = true;
                }
            }
        }
    }
}