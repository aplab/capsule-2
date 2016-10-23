<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 14.12.2013 22:05:35 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Table\Indexes\Fields;

use Capsule\DataModel\Config\AbstractConfig;

/**
 * Fields.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Fields extends AbstractConfig
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        foreach ($data as $field_name => $field_data) {
            if (is_null($field_data)) {
                // полю присвоили null
                // удаление ключа поля
                unset($this->data[$field_name]);
                continue;
            }
            $this->data[$field_name] = new Field($field_data);
        }
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString()
    {
        $tmp = array();
        $counter = 0;
        foreach ($this->data as $name => $field) {
            $tmp[] = array(
                'name' => $name,
                'field' => $field,
                'counter' => $counter,
                'position' => $field->get('position', 0)
            );
            $counter++;
        }
        usort($tmp, function ($a, $b) {
            if ($a['position'] == $b['position']) {
                return ($a['counter'] < $b['counter']) ? -1 : 1;
            }
            return ($a['position'] < $b['position']) ? -1 : 1;
        });
        $ret = array();
        foreach ($tmp as $data_item) {
            $param = $data_item['field']->toString();
            $ret[] = '`' . $data_item['name'] . '`' . ($param ? ' ' . $param : '');
        }
        return join(', ', $ret);
    }
}