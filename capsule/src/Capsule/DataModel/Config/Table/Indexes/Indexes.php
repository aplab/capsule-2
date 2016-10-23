<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 23:20:54 YEKT 2013                                              |
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
use Capsule\Tools\Tools;

/**
 * Indexes.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Indexes extends AbstractConfig
{
    /**
     * Default index type
     *
     * @var string
     */
    const DEFAULT_TYPE = 'KEY';

    /**
     * @param array $data
     * @return self
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        foreach ($data as $index_name => $index_data) {
            if (is_null($index_data)) {
                // индексу присвоили null
                // удаление ключа индекса
                unset($this->data[$index_name]);
                continue;
            }
            $this->data[$index_name] = new Index($index_data);
        }
    }

    /**
     * explicit conversion to string
     *
     * @param int $indent
     * @return string
     */
    public function toString($indent = 0)
    {
        $ret = array();
        foreach ($this->data as $name => $index) {
            $type = $index->get('type', self::DEFAULT_TYPE);
            if (preg_match('/^primary/i', $type)) {
                $tmp = 'PRIMARY KEY ';
            } elseif (preg_match('/^unique/i', $type)) {
                $tmp = 'UNIQUE KEY `' . $name . '` ';
            } elseif (preg_match('/^fulltext/i', $type)) {
                $tmp = 'FULLTEXT KEY `' . $name . '` ';
            } else {
                $tmp = 'KEY `' . $name . '` ';
            }
            $ret[] = str_repeat(' ', $indent) . $tmp . sprintf($index->toString(), $name);
        }
        return join(', ' . chr(10), $ret);
    }
}