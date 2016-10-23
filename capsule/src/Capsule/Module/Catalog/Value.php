<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 19 мая 2015 г. 22:24:15 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module\Catalog;

use Capsule\Core\Singleton;
use Capsule\DataModel\Inflector;
use Capsule\Db\Db;
use Capsule\Core\Fn;
/**
 * Value.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Value extends Singleton
{
    /**
     * Связанная таблица
     *
     * @var string
     */
    protected $table;

    /**
     * Уже полученные данные из таблицы
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Несохраненные данные
     *
     * @var array
     */
    protected $unsaved = array();

    /**
     * Защищенный конструктор
     *
     * @param void
     * @return self
     * @throws Exception
     */
    protected function __construct() {
        $this->table = Inflector::getInstance()->getAssociatedTable($this);
        $db = Db::getInstance();
        if (!$db->tableExists($this->table)) {
            $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (

                    `product_id` BIGINT UNSIGNED NOT NULL COMMENT "идентификатор товара",
                    `attribute_id` BIGINT UNSIGNED NOT NULL COMMENT "идентификатор атрибута",

                    `integer` BIGINT NOT NULL DEFAULT 0 COMMENT "Value if type is signed integer",
                    `unsigned_integer` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT "Value if type is unsigned integer",
                    `string` VARCHAR(255) NOT NULL DEFAULT "" COMMENT "Value if type is string",
                    `text` TEXT COMMENT "Value if type is text",

                    PRIMARY KEY (`product_id`, `attribute_id`))
                    ENGINE = InnoDB COMMENT = ' . $db->qt(__CLASS__);
            $db->query($sql);
            if (!$db->tableExists($this->table, true)) {
                $msg = 'Unable to create table ' . $this->table;
                throw new \Exception($msg);
            }
        }
    }

    /**
     * Возвращает значения атрибутов продукта.
     *
     * @param Product|int $product
     * @return mixed
     */
    public function product($product) {
        $product_id = ($product instanceof Product) ? $product->get('id') : intval($product, 10);
        if (!array_key_exists($product_id, $this->cache)) {
            $db = Db::getInstance();
            $table_attr = forward_static_call(array(Fn::cc('Attribute', $this), 'config'))->table->name;
            $sql = 'SELECT `val`.*, `attr`.`type`
                    FROM `' . $this->table . '` AS `val`
                    INNER JOIN `' . $table_attr . '` AS `attr`
                    ON `val`.`attribute_id` = `attr`.`id`
                    WHERE `val`.`product_id` = ' . $db->qt($product_id);
            $data = $db->query($sql);
            $tmp = array();
            foreach ($data as $i) if (array_key_exists($i['type'], $i)) $tmp[$i['attribute_id']] = $i[$i['type']];
            $this->cache[$product_id] = $tmp;
        }
        return $this->cache[$product_id];
    }

    /**
     * @param Product $object
     * @param Attribute $attr
     * @param mixed $value
     */
    public function set(Product $product, Attribute $attr, $value) {
        $product_id = $product->id;
        $attribute_id = $attr->id;
        $this->cache[$product_id][$attribute_id] = $value;
        $this->unsaved[$product_id][$attribute_id] = $value;
    }

    /**
     * Clear all data
     *
     * @param void
     * @return void
     */
    public function clear() {
        $this->cache = array();
        $this->unsaved = array();
    }

    /**
     * Save all unsaved
     *
     * @param void
     * @return void
     */
    public function store() {
        if (empty($this->unsaved)) return;
        $db = Db::getInstance();
        $sql = 'REPLACE INTO `' . $this->table . '` (
                    `product_id`,
                    `attribute_id`,
                    `integer`,
                    `unsigned_integer`,
                    `string`,
                    `text`
                ) VALUES ';
        $lines = array();
        foreach ($this->unsaved as $product_id => $data) {
            foreach ($data as $attribute_id => $value) {
                $line = array($db->qt($product_id));
                $line[] = $db->qt($attribute_id);
                $line[] = $db->qt(preg_filter('/^\\d+$/', '$0', $value) ?: 0);
                $line[] = $db->qt(preg_filter('/^-?\\d+$/', '$0', $value) ?: 0);
                $line[] = $db->qt(preg_filter('/^.{1,255}$/u', '$0', $value) ?: '');
                $line[] = $db->qt(preg_filter('/^.{1,65535}$/u', '$0', $value) ?: '');
                $lines[] = '(' . join(',', $line) . ')';
            }
        }
        if (empty($lines)) return;
        $sql .= join(',', $lines);
        $db->query($sql);
        $this->unsaved = array();
    }
}