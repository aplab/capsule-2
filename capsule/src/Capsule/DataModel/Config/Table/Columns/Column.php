<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.12.2013 1:05:12 YEKT 2013                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\DataModel\Config\Table\Columns;

use Capsule\DataModel\Config\AbstractConfig;

/**
 * Column.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $comment
 * @property boolean $nullable
 */
class Column extends AbstractConfig
{
    const TYPE = 'type';

    public function __construct(array $data) {
        parent::__construct($data);
        if (array_key_exists(self::TYPE, $this->data)) {
            $this->data[self::TYPE] = strtoupper($this->data[self::TYPE]);
        }
    }

    /**
     * implicit conversion to a string
     *
     * @param void
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * explicit conversion to string
     *
     * @param void
     * @return string
     */
    public function toString() {
        $str = array($this->type);
        if (isset($this->length)) {
            $tmp = array('(' . $this->length);
            if (isset($this->decimals)) {
                $tmp[] = ', ' . $this->decimals;
            }
            $tmp[] = ')';
            $str[] = join($tmp);
        } elseif (isset($this->variants) && is_array($this->variants)) {
            /**
             * @TODO add ENUM and SET support needed
             */
        }
        if ($this->get('unsigned')) {
            $str[] = 'UNSIGNED';
        }
        if ($this->get('zerofill')) {
            $str[] = 'ZEROFILL';
        }
        if (!$this->get('nullable')) {
            $str[] = 'NOT NULL';
        } else {
            $str[] = 'NULL';
        }
        if (isset($this->default)) {
            $default = $this->default;
            $tmp = array('DEFAULT');
            if (is_null($default)) {
                $tmp[] = 'NULL';
            } else {
                if (is_string($default)) {
                    if ('TIMESTAMP' === $this->type) {
                        if ('CURRENT_TIMESTAMP' === strtoupper($default)) {
                            $tmp[] = 'CURRENT_TIMESTAMP';
                        } elseif ('0' === strval($default)) {
                            $tmp[] = '0';
                        } else {
                            $tmp[] = '"' . $default . '"';
                        }
                    } else {
                        $tmp[] = '"' . $default . '"';
                    }
                } else {
                    $tmp[] = $default;
                }
            }
            $str[] = join(' ', $tmp);
        }
        if (isset($this->onUpdate)) {
            $str[] = 'ON UPDATE CURRENT_TIMESTAMP';
        }
        if ($this->get('autoIncrement')) {
            $str[] = 'AUTO_INCREMENT';
        }
        if ($this->get('comment')) {
            $str[] = 'COMMENT "' . $this->comment . '"';
        }
        return join(' ', $str);
    }
}