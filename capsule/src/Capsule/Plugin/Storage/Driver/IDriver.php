<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 17:43:36 YEKT 2014                                              |
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

/**
 * IDriver.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
interface IDriver
{
    /**
     * Добавляет файл в хранилище.
     * $source_absolute_path - место, откуда взять файл.
     * 
     * @param string $source_absolute_path
     * @param string $extension
     * @return string
     */
    function addFile($source_absolute_path, $extension = null);
    
    function delFile($filename);
}