<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 23:22:34 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Table\Indexes;

use Capsule\DataModel\Config\AbstractConfig;
use Capsule\DataModel\Config\Table\Indexes\Fields\Fields;

/**
 * Index.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Index extends AbstractConfig
{
    /**
     * special properties
     *
     * @var string
     */
    const FIELDS = 'fields';

    /**
     * @param array $data
     * @return self
     */
    public function __construct(array $data) {
        parent::__construct($data);
        if (array_key_exists(self::FIELDS, $this->data)) {
            $this->data[self::FIELDS] =
            new Fields($this->data[self::FIELDS]);
        }
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        if (sizeof($this->fields)) {
            return '(' . $this->fields->toString() . ')';
        }
        return '';
    }
}