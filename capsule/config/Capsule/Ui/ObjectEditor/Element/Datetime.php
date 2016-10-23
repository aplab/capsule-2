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
use Capsule\DateTime\DateTime as d;
/**
 * Datetime.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Datetime extends Element
{
    public function __construct(DataModel $object, $name, $settings = array()) {
        parent::__construct($object, $name, $settings);
        if (!$this->hasValue && $this->settings->defaultNow) {
            $this->data['hasValue'] = true;
            $val = new d;
            $this->data['value'] = $val->getMysqlDatetime();
        }
    }
}