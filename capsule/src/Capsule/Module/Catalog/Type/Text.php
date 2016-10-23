<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 25 мая 2015 г. 23:57:10 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module\Catalog\Type;

/**
 * Text.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Text extends Type
{
    protected static $json = <<<'JSON'
    {
        "title":"Untitled string",
        "description":"",
        "help":"",
        "comment":"",
        "name":"",
        "label":"",
        "validator":{
            "type":"StringLength",
            "max":65530
        },
        "formElement":{
            "f1":{
                "type":"Text",
                "order":1000000,
                "tab":"Attribute"
            }
        }
    }
JSON;
}