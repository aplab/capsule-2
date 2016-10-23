<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 16.06.2013 2:33:15 YEKT 2013                                             |
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

use Capsule\Db\Result;
use Capsule\DataModel\Inflector;
use Capsule\Db\Db;
use Capsule\Core\Fn as f;
use Capsule\DataModel\DataModel;

/**
 * SingleKey.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class SingleKey extends DataModel
{
    /**
     * Первичный ключ модуля
     *
     * @var string
     */
    protected static $key;

    /**
     * Возвращает данные в виде объектов
     *
     * @param Result $result
     * @return array
     */
    protected static function populate(Result $result)
    {
        $class = get_called_class();
        $data = $result->fetch_assoc();
        $ret = array();
        if ($data) {
            $properties = Inflector::getInstance()->getAssociatedProperties(array_keys($data));
            $has_key = array_key_exists(static::$key, $data);
        } else {
            return $ret;
        }
        if ($has_key) {
            while ($data) {
                $key = $data[static::$key];
                if (isset(self::$cache[$class][$key])) {
                    $ret[$key] = self::$cache[$class][$key];
                } else {
                    $o = new $class;
                    $o->data = array_combine($properties, $data);
                    $ret[$key] = self::$cache[$class][$key] = $o;
                }
                $data = $result->fetch_assoc();
            }
        } else {
            while ($data) {
                $o = new $class;
                $o->data = array_combine($properties, $data);
                $ret[] = $o;
                $data = $result->fetch_assoc();
            }
        }
        return $ret;
    }

    /**
     * store object into database
     *
     * @return boolean|number
     */
    public function store()
    {
        if (isset($this->data[static::$key])) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Сохраняет объект в связанную таблицу базы данных.
     * Возвращает присвоенный идентификатор.
     * @return int
     * @throws Exception
     * @param void
     */
    protected function insert()
    {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $fields = $db->listFields($table);
        $properties = Inflector::getInstance()->getAssociatedProperties($fields);
        $map = array_combine($properties, $fields);
        $values = array();
        foreach ($this->data as $property => $value) {
            if (!isset($map[$property])) {
                continue;
            }
            if (is_null($value)) {
                $value = 'null';
            } else {
                $value = $db->qt($value);
            }
            $values[$map[$property]] = $value;
        }
        if (empty($values)) {
            $sql = 'INSERT INTO ' . $db->bq($table) . ' VALUES()';
        } else {
            $sql = 'INSERT INTO ' . $db->bq($table) . ' (' .
                join(', ', $db->bq(array_keys($values))) . ')
                    VALUES (' . join(', ', $values) . ')';
        }
        $db->query($sql);
        if ($db->errno) {
            throw new Exception($db->error);
        }
        $key = $db->insert_id;
        $this->data[static::$key] = $key;
        return $key;
    }

    /**
     * Обновляет объект в связанной таблице базы данных.
     * Возвращает
     * @return bool
     * @throws Exception
     * @param void
     */
    protected function update()
    {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $fields = $db->listFields($table);
        $properties = Inflector::getInstance()
            ->getAssociatedProperties($fields);
        $map = array_combine($properties, $fields);
        $fragments = array();
        foreach ($this->data as $property => $value) {
            if (static::$key === $property) { // featured KeyBasedModule
                continue;
            }
            if (!isset($map[$property])) {
                continue;
            }
            if (is_null($value)) {
                $value = 'null';
            } else {
                $value = $db->qt($value);
            }
            $fragments[] = f::concat_ws(' = ', $db->bq($map[$property]), $value);
        }
        if (empty($fragments)) {
            return true;
        }
        $sql = 'UPDATE ' . $db->bq($table) . '
                SET ' . join(', ', $fragments) . '
                WHERE `' . static::$key . '` = ' . $db->qt($this->id);
        $db->query($sql);
        if ($db->errno) {
            throw new Exception($db->error);
        }
        return $db->affected_rows;
    }

    /**
     * Cloning
     *
     * @param void
     * @return self
     */
    public function __clone()
    {
        unset($this->data[static::$key]);
    }

    /**
     * Возвращает объект по его ключу
     *
     * @param mixed $key
     * @return self
     */
    public static function getElementByKey($key)
    {
        return static::k($key);
    }

    /**
     * Возвращает объект по его ключу
     *
     * @param mixed $key
     * @return self
     */
    public static function k($key)
    {
        $o = self::getElementByKeyFromCache($key);
        if ($o) {
            return $o;
        }
        $db = Db::getInstance();
        $table = $db->bq(self::config()->table->name);
        $sql = 'SELECT *
                FROM ' . $table . '
                WHERE `' . static::$key . '` = ' . $db->qt($key);
        $objects = self::populate($db->query($sql));
        // array_shift returns NULL if array is empty
        return array_shift($objects);
    }

    /**
     * Возвращает массив ключей
     *
     * @param void
     * @return array
     */
    public static function keys()
    {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $db = Db::getInstance();
            $table = $db->bq(self::config()->table->name);
            $sql = 'SELECT `' . static::$key . '`
                    FROM ' . $table;
            self::$common[$class][__FUNCTION__] =
                $db->query($sql)->fetch_col();
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Возвращает объект по его ключу, если такой есть в кэше
     *
     * @param mixed $key
     * @return self
     */
    public static function kfc($key)
    {
        $class = get_called_class();
        if (isset(self::$cache[$class][static::$key])) {
            return self::$cache[$class][static::$key];
        }
        return null;
    }

    /**
     * Возвращает объект по его ключу, если такой есть в кэше
     *
     * @param mixed $key
     * @return self
     */
    public static function getElementByKeyFromCache($key)
    {
        return static::kfc($key);
    }

    /**
     * Возвращает страницу ообъектов из связанной таблицы
     *
     * @param int $page_number
     * @param int $items_per_page
     * @return array
     */
    public static function page($page_number = 1, $items_per_page = 10)
    {
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                ORDER BY ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }
}