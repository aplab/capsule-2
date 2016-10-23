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
/**
 * Tree.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Tree extends Element
{
    public function __construct(DataModel $object, $name, $settings) {
        parent::__construct($object, $name, $settings);
        $class = get_class($object);
        $this->data['options'] = $class::optionsDataList();
        if ($settings instanceof FormElement) {
            $default = $settings->default;
            $default = str_replace('__CLASS__', get_class($object), $default);
            $this->data['default'] = Env::getInstance()->get($default);
        }
    }
}