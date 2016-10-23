<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 07.06.2014 9:06:02 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Unit\Nested;

use PHP\Exceptionizer\Exceptionizer;
use Capsule\Db\Db;
use Capsule\Core\Fn;
use Capsule\Unit\UnitTsUsr;

/**
 * Item.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Item extends UnitTsUsr
{
    use \Capsule\Traits\sortOrder;

    /**
     * Возвращает все элементы контейнера
     *
     * @param int $container_id
     * @return array
     */
    public static function getElementsByContainer($container_id)
    {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` = ' . $db->qt($container_id) . '
                ORDER BY ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }

    /**
     * Возвращает количество элементов в контейнере
     *
     * @param int $container_id
     * @return int
     */
    public static function numberByContainer($container_id)
    {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return 0;
        $db = Db::getInstance();
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
                WHERE `container_id` = ' . $db->qt($container_id);
        return $db->query($sql)->fetch_one();
    }

    /**
     * Возвращает страницу ообъектов из связанной таблицы
     *
     * @param $container_id
     * @param int $page_number
     * @param int $items_per_page
     * @return array
     */
    public static function pageByContainer($container_id, $page_number = 1, $items_per_page = 10)
    {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` = ' . $db->qt($container_id) . '
                ORDER BY ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }

    /**
     * Returns pages number
     *
     * @param $container_id
     * @param int|number $items_per_page
     * @return array
     */
    public static function pagesByContainer($container_id, $items_per_page = 10)
    {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $c = self::numberByContainer($container_id);
        if (!$c) {
            return array();
        }
        return range(1, ceil($c / $items_per_page));
    }

    /**
     * Возвращает элементы у которых есть привязка к контейнеру
     *
     * @param void
     * @return array
     */
    public static function getElementsWithContainer()
    {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        $in = array();
        $db = Db::getInstance();
        array_walk($keys, function ($v, $k) use (&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` IN(' . join(', ', $in) . ')
                ORDER BY ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }

    /**
     * Возвращает элементы у которых нет привязки к контейнеру
     *
     * @param void
     * @return array
     */
    public static function getElementsWithoutContainer()
    {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        $in = array();
        $db = Db::getInstance();
        array_walk($keys, function ($v, $k) use (&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` NOT IN(' . join(', ', $in) . ')
                ORDER BY ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }

    /**
     * Возвращает количество элементов у которых есть привязка к контейнеру
     *
     * @param void
     * @return int
     */
    public static function numberWithContainer()
    {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        if (empty ($keys)) return 0;
        $in = array();
        $db = Db::getInstance();
        array_walk($keys, function ($v, $k) use (&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
                WHERE `container_id` IN(' . join(', ', $in) . ')';
        return $db->query($sql)->fetch_one();
    }

    /**
     * Возвращает количество элементов у которых нет привязки к контейнеру
     *
     * @param void
     * @return array
     */
    public static function numberWithoutContainer()
    {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        if (empty($keys)) return self::number();
        $in = array();
        $db = Db::getInstance();
        array_walk($keys, function ($v, $k) use (&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
                WHERE `container_id` NOT IN(' . join(', ', $in) . ')';
        return $db->query($sql)->fetch_one();
    }

    /**
     * Returns pages number
     *
     * @param int|number $items_per_page
     * @return array
     */
    public static function pagesWithContainer($items_per_page = 10)
    {
        $c = self::numberWithContainer();
        if (!$c) return array();
        return range(1, ceil($c / $items_per_page));
    }

    /**
     * Returns pages number
     *
     * @param int|number $items_per_page
     * @return array
     */
    public static function pagesWithoutContainer($items_per_page = 10)
    {
        $c = self::numberWithoutContainer();
        if (!$c) return array();
        return range(1, ceil($c / $items_per_page));
    }

    /**
     * Возвращает элементы у которых есть привязка к контейнеру
     *
     * @param int $page_number
     * @param int $items_per_page
     * @return array
     */
    public static function pageWithContainer($page_number = 1, $items_per_page = 10)
    {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        if (empty($keys)) return array();
        $in = array();
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        array_walk($keys, function ($v, $k) use (&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` IN(' . join(', ', $in) . ')
                ORDER BY ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }

    /**
     * Возвращает элементы у которых нет привязки к контейнеру
     *
     * @param int $page_number
     * @param int $items_per_page
     * @return array
     */
    public static function pageWithoutContainer($page_number = 1, $items_per_page = 10)
    {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        if (empty($keys)) return self::page($page_number, $items_per_page);
        $in = array();
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        array_walk($keys, function ($v, $k) use (&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` NOT IN(' . join(', ', $in) . ')
                ORDER BY ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }
}