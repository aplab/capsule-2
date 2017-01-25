<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 18:21:57 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Plugin\Storage\Driver;
use Capsule\Component\Config\Config;

/**
 * Driver.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Driver implements IDriver
{
    /**
     * @var Config
     */
    protected $config;
    
    /**
     * @param Config $config
     * @return self;
     */
    public function __construct(Config $config) {
        $this->config = $config;
    }
}