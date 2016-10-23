<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 03.10.2014 12:34:28 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Model;

use Capsule\Unit\NamedTsUsr;
use Capsule\Db\Db;
/**
 * HistoryUploadImage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class HistoryUploadImage extends NamedTsUsr
{
    /**
     * Сколько записей истории хранить
     * 
     * @var int
     */
    const LIMIT = 1000;
    
    /**
     * Добавляет или убирает избранное
     * 
     * @param boolean $value
     * @return void
     */
    protected function setFavorites($value) {
        $this->data['favorites'] = $value ? 1 : 0;
    }
    
    /**
     * Удаляет записи истории с таким же значением пути, но с другими id.
     * Возвращает количество удаленных записей.
     * Не удаляет из избранного
     * 
     * @param self $item
     * @return int
     */
    public static function deleteSamePath(self $item) {
        $db = Db::getInstance();
        $sql = 'DELETE FROM ' . $db->bq(self::config()->table->name) . '
                WHERE `path` = ' . $db->qt($item->get('path', '')) . '
                AND `storage` = ' . $db->qt($item->get('storage', '')) . '
                AND `favorites` = 0
                AND `id` != ' . $db->qt($item->get('id', 0));
        $db->query($sql);
        return $db->affected_rows;
    }
    
    /**
     * Сборщик мусора. Удаляет записи если записей больше лимита.
     * Не удаляет избранное.
     * Возвращает количество удаленных записей. 
     * 
     * @param void
     * @return int
     */
    public static function gcCollect() {
        $db = Db::getInstance();
        $sql = 'DELETE FROM ' . $db->bq(self::config()->table->name) . '
                WHERE `favorites` = 0
                AND `id` < (
                    SELECT `id` FROM (
                        SELECT `id` FROM ' . $db->bq(self::config()->table->name) . '
                        WHERE `favorites` = 0
                        ORDER BY `id` DESC
                        LIMIT ' . self::LIMIT - 1 . ', 1
                    ) AS `t`
                )';
        $db->query($sql);
        return $db->affected_rows;
    }
    
    /**
     * Возвращает историю
     * 
     * @param void
     * @return \Capsule\Db\Result
     */
    public static function history() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM ' . $db->bq(self::config()->table->name) . '
                ORDER BY `favorites` DESC, `id` DESC';
        return $db->query($sql); 
    }
    
    /**
     * Возвращает избранное
     *
     * @param void
     * @return \Capsule\Db\Result
     */
    public static function favorites() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM ' . $db->bq(self::config()->table->name) . '
                WHERE `favorites` = 1
                ORDER BY `id` DESC';
        return $db->query($sql);
    }
}