<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 12.07.2014 8:45:23 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module;

use Capsule\Unit\TitledTsUsr;
use Capsule\Db\Db;

/**
 * DevLog.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DevLog extends TitledTsUsr 
{
    use \Capsule\Traits\setActive;
    
    /**
     * Возвращает количество элементов у которых установлен флаг active
     * и дата не позже текущей даты. Элементы из "будущего" не считать.
     * 
     * @param boolean $nocache
     * @return int
     */
    public static function numberActualActive($nocache = null) {
        $ck = self::ck();// common key
        if ($nocache || !array_key_exists($ck, self::$common)) {
            $db = Db::getInstance();
            $table = $db->bq(self::config()->table->name);
            $sql = 'SELECT COUNT(*) FROM ' . $table . '
                    WHERE `active`
                    AND `datetime` <= NOW()';
            self::$common[$ck] = $db->query($sql)->fetch_one();
        }
        return self::$common[$ck];
    }
}