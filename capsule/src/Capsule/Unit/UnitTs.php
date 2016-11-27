<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 31.05.2014 9:29:07 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Unit;

use Capsule\Db\Db;
use Capsule\DataModel\Inflector;
use Capsule\Core\Fn;
use Capsule\Model\Exception;

/**
 * UnitTs.php
 * С меткой времени создания и последнего изменения.
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class UnitTs extends Unit
{
    /**
     * Special fields
     *
     * @var string
     */
    const CREATED = 'created', LAST_MODIFIED = 'last_modified';

    /**
     * Сохраняет объект в связанную таблицу базы данных.
     * Возвращает присвоенный идентификатор.
     *
     * @return int
     * @throws Exception
     * @param void
     */
    protected function insert()
    {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $fields = $db->listFields($table);
        $properties = Inflector::getInstance()
            ->getAssociatedProperties($fields);
        $map = array_combine($properties, $fields);
        $values = array();
        foreach ($this->data as $property => $value) {
            if (!isset($map[$property])) {
                continue;
            }
            if (self::CREATED === $map[$property]) { // skip special property
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
            $sql = 'INSERT INTO ' . $db->bq($table) . '() VALUES ()';
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
     *
     * @return bool
     * @throws Exception
     * @param void
     */
    protected function update()
    {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $fields = $db->listFields($table);
        $properties = Inflector::getInstance()->getAssociatedProperties($fields);
        $map = array_combine($properties, $fields);
        $fragments = array();
        foreach ($this->data as $property => $value) {
            if (!isset($map[$property])) {
                continue;
            }
            if (static::$key === $property) { // featured KeyBasedModule special property
                continue;
            }
            if (self::CREATED === $map[$property]) { // skip special property
                continue;
            }
            if (self::LAST_MODIFIED === $map[$property]) { // skip special property
                continue;
            }
            if (is_null($value)) {
                $value = 'null';
            } else {
                $value = $db->qt($value);
            }
            $fragments[] = Fn::concat_ws(' = ', $db->bq($map[$property]), $value);
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
     * Disable set special property "created" directly
     *
     * @param string $value
     * @param string $name
     * @return void
     */
    final protected function setCreated($value, $name)
    {
    }

    /**
     * Disable set special property "last modified" directly
     *
     * @param string $value
     * @param string $name
     * @return void
     */
    final protected function setLastModified($value, $name)
    {
    }
}