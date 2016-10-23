<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 26.05.2013 11:44:47 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Model;

use Capsule\Common\Filter;
use Capsule\Db\Db;

/**
 * IdBased.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class IdBased extends SingleKey
{
    /**
     * Первичный ключ модуля
     *
     * @var string
     */
    protected static $key = 'id';

    /**
     * Is key
     *
     * @param string $key
     * @return boolean
     */
    public static function isKey($key)
    {
        return static::$key === $key;
    }

    /**
     * Возвращает объект по его идентификатору, если такой есть в кэше
     *
     * @param int $id
     * @return self
     */
    public static function getElementById($id)
    {
        return self::id($id);
    }

    /**
     * Возвращает объект по его идентификатору, если такой есть в кэше
     *
     * @param int $id
     * @return self
     */
    public static function id($id)
    {
        $id = Filter::id($id);
        if (!$id) {
            return null;
        }
        return self::getElementByKey($id);
    }

    /**
     * Обрабатывает (запрещает) изменение свойства id
     *
     * @param $value
     * @return IdBased
     * @throws Exception
     * @internal param $string
     */
    final protected function setId($value)
    {
        $msg = 'Cannot set readonly property: ' . get_class($this) . '::$id';
        throw new Exception($msg);
    }

    /**
     * Удаляет данные объекта из таблицы.
     * Возвращает количество затронутых строк.
     *
     * @param int $id
     * @return int
     */
    public static function del($id)
    {
        $tmp = $id;
        if (!is_array($tmp)) {
            $tmp = array($tmp);
        }
        $ids = array();
        $db = Db::getInstance();
        foreach ($tmp as $id) {
            if (ctype_digit($id)) {
                $ids[] = $db->qt($id);
            }
        }
        if (empty($ids)) {
            return;
        }
        $table = $db->bq(self::config()->table->name);
        $sql = 'DELETE
                FROM ' . $table . '
                WHERE `' . static::$key . '` IN(' . join(',', $ids) . ')';
        $db->query($sql);
        return $db->affected_rows;
    }
}