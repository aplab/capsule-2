<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 30.01.2014 23:41:52 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Util;

use Capsule\Core\Singleton;
/**
 * Keygen.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Keygen extends Singleton
{
    /**
     * Диапазон символов для формирования ключа
     * 
     * @var array
     */
    protected $range = array();
    
    /**
     * @param void
     * @return self
     */
    protected function __construct() {
        $this->range = array_merge( 
            range(0, 9), 
            range('a', 'z'), 
            range('A', 'Z'), 
            array('-', '_')
        );
    }
    
    /**
     * @param number $length
     * @param number $range
     * @return string
     */
    public function generate($length = 128, $tail_length = 10) {
        $number = sizeof($this->range) - 1;
        $length = $length + mt_rand(0, $tail_length);
        $prev = '';
        $key = '';
        while ($length) {
            shuffle($this->range);
            $symbol = (string)$this->range[mt_rand(0, $number)];
            if ($symbol === $prev) {
                continue;
            }
            $prev = $symbol;
            $length--;
            $key.= $symbol;
        }
        return $key;
    }
}