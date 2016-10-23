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

namespace Capsule\Unit;

use Capsule\Unit\NamedTsUsr;
use Capsule\Db\Db;
use PHP\Exceptionizer\Exceptionizer;
use Capsule\I18n\I18n;

/**
 * Tree.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Tree extends NamedTsUsr
{
    use \Capsule\Traits\sortOrder;
    
    /**
     * returns the roots
     *
     * @param void
     * @return array
     */
    public static function roots() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `parent_id` = 0
                ORDER BY `sort_order`, ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }
    
    /**
     * returns the roots
     *
     * @param void
     * @return array
     */
    public static function countRoots() {
        $db = Db::getInstance();
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
	            WHERE `parent_id` = 0';
        return $db->query($sql)->fetch_one();
    }
    
    /**
     * Возвращает список options для select
     * 
     * @param void
     * @return array
     */
    public static function optionsDataList() {
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $tmp = $class::tree();
            array_walk($tmp, function($v, $k) use (& $tmp) {
                $tmp[$k] = array(
                    'value' => $k,
                    'text' => str_repeat('. ', $v->level) . $v->name,
                    'selectd' => false
                );
            });
            self::$common[$class][__FUNCTION__] = $tmp;
        }
        return self::$common[$class][__FUNCTION__];
    }
    
    /**
     * Задает id родителя 
     * 
     * @param int $v
     * @param string $n
     * @throws \Exception
     * @return self
     */
    protected function setParentId($v, $n) {
        if ($v === $this->get('id')) {
            $msg = I18n::_('It is impossible to move object into itself.');
            throw new \Exception($msg);
        }
        if ($this->isAncestorOf(static::getElementById($v))) {
            $msg = I18n::_('It is impossible to move object into his descendant.');
            throw new \Exception($msg);
        }
        $this->data[$n] = $v;
        return $this;
    }
    
    /**
     * Проверяет, является ли объект потомком данного объекта
     *
     * @param self $item
     * @return boolean
     */
    public function isDescendantOf($item) {
        if (!($item instanceof $this)) {
            return false;
        }
        $parent = $this->parentElement();
        while ($parent instanceof $this) {
            if ($parent === $item) {
                return true;
            }
            $parent = $parent->parentElement();
        }
        return false;
    }
    
    /**
     * Проверяет, является ли объект предком данного объекта
     *
     * @param self $item
     * @return boolean
     */
    public function isAncestorOf($item) {
        if (!($item instanceof $this)) {
            return false;
        }
        $parent = $item->parentElement();
        while ($parent instanceof $this) {
            if ($parent === $this) {
                return true;
            }
            $parent = $parent->parentElement();
        }
        return false;
    }
    
    /**
     * Упорядочивает переданные объекты в виде двумерного массива
     * [parent_id][id] = object
     *
     * @param mixed
     * @return array
     */
    public static function to2d() {
        if (!func_num_args()) return array();
        $tmp = array();
        $a = func_get_args();
        array_walk_recursive($a, function($v, $k) use (& $tmp) {
            if ($v instanceof self) {
                $parent_id = $v->parentId;
                $id = $v->{static::$key};
                if ($id) $tmp[$parent_id][$id] = $v;
            }
        });
        return $tmp;
    }
    
    /**
     * returns the direct descendants of the object
     *
     * @param void
     * @return array
     */
    public function children() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `parent_id` = ' . $db->qt($this->id) . '
                ORDER BY `sort_order`, ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }
    
    /**
     * Возвращает ветку
     *
     * @param self $from
     * @return array
     */
    public static function branch(self $from = null) {
        $tmp = array();
        $children = self::childrenOf($from);
        while ($children) {
            $tmp += $children;
            $children = self::childrenOf($children);
        }
        return $tmp;
    }
    
    /**
     * returns the parent of the object
     *
     * @param void
     * @return self
     */
    public function parentElement() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `id` = ' . $db->qt($this->parentId);
        $objects = self::populate($db->query($sql));
        // array_shift returns NULL if array is empty
        return array_shift($objects);
    }
    
    /**
     * Возвращает детей
     * Любое количество любых параметров, если без параметров возвращает пустой массив.
     *
     * @param mixed
     * @return array
     */
    public static function childrenOf() {
        if (!func_num_args()) return array();
        $e = new Exceptionizer();
        $in = array();
        $a = func_get_args();
        $db = Db::getInstance();
        array_walk_recursive($a, function($v, $k) use (& $in, $db) {
            if ($v instanceof self) {
                $v = $v->get(static::$key);
            }
            settype($v, 'string');
        	if (!$v) {
        	    $in[0] = 0;
        	    return;
        	}
        	$in[$v] = $db->qt($v);
        });
        if (empty($in)) return array();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `parent_id` IN (' . join(', ', $in) . ')
                ORDER BY `sort_order`, ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }
    
    /**
     * Возвращает дерево
     *
     * @param void
     * @return array
     */
    public static function tree() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            ORDER BY `parent_id`, `sort_order`, ' . $db->bq(static::$key);
        $tmp = static::populate($db->query($sql));
        $tmp = self::to2d($tmp);
        $ret = array();
        $level = 0;
        $to_list = function($from_key = 0) use (& $ret, $tmp, & $to_list, & $level) {
            if (!isset($tmp[$from_key])) return;
            foreach ($tmp[$from_key] as $k => $v) {
                $v->level = $level;
                $ret[$k] = $v;
                if (isset($tmp[$k])) {
                    $level++;
                    $to_list($k);
                    $level--;
                }
            }
        };
        $to_list(0);
        return $ret;
    }
    
    /**
     * Returns pages number
     *
     * @param number $items_per_page
     * @return array
     */
    public static function pages($items_per_page = 10) {
        $c = self::countRoots();
        if (!$c) {
            return array();
        }
        return range(1, ceil($c / $items_per_page));
    }
    
    /**
     * Возвращает страницу ообъектов из связанной таблицы
     *
     * @param int $page_number
     * @param int $items_per_page
     */
    public static function page($page_number = 1, $items_per_page = 10) {
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `parent_id` = 0
                ORDER BY `sort_order`, ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        $roots = static::populate($db->query($sql));
        $tmp = array();
        $children = $roots;
        $level = 0;
        while ($children) {
            array_walk($children, function($v, $k) use ($level) {
                $v->level = $level;
            });
            $tmp += $children;
            $children = self::childrenOf($children);
            $level++;
        }
        $tmp = static::to2d($tmp);
        $ret = array();
        $level = 0;
        $to_list = function($from_key = 0) use (& $ret, $tmp, & $to_list, & $level) {
            if (!isset($tmp[$from_key])) return;
            foreach ($tmp[$from_key] as $k => $v) {
                $v->levelCheck = $level;
                $ret[$k] = $v;
                if (isset($tmp[$k])) {
                    $level++;
                    $to_list($k);
                    $level--;
                }
            }
        };
        $to_list(0);
        return $ret;
    }
    
    /**
     * Возвращает дерево в виде двумерного массива
     * с проверкой целостности дерева
     *
     * @param void
     * @return array
     */
    public static function tree2d() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            ORDER BY `parent_id`, `sort_order`, ' . $db->bq(static::$key);
        $tmp = static::populate($db->query($sql));
        $tmp = self::to2d($tmp);
        $ret = array();
        $filter = function($from_key = 0) use (& $filter, & $tmp, & $ret) {
            if (!isset($tmp[$from_key])) return;
            $ret[$from_key] = $tmp[$from_key];
            foreach ($tmp[$from_key] as $k => $v) {
                if (isset($tmp[$k])) $filter($k);
            }
        };
        $filter();
        return $ret;
    }
    
    /**
     * Восстанавливает элементы, у которых по какой-либо причине не найден
     * родительский элемент, или возникла петля, циклическая ссылка, переводя 
     * их на нулевой уровень, в результате чего они становятся видны в списке.
     *
     * Возвращает массив ключей таких элементов.
     *
     * @param void
     * @return int
     */
    public static function repair() {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name;
        $all = static::populate($db->query($sql));
        $tmp2d = self::to2d($all);
        $good = array();
        $filter = function($from_key = 0) use (& $filter, & $tmp2d, & $good) {
            if (!isset($tmp2d[$from_key])) return;
            foreach ($tmp2d[$from_key] as $k => $v) {
                $good[$k] = $v;
                if (isset($tmp2d[$k])) $filter($k);
            }
        };
        $filter();
        $corrupted = array_diff_key($all, $good);
        if (empty($corrupted)) return array();
        $in = array();
        $keys = array_keys($corrupted);
        array_walk($keys, function($v, $k) use (& $in, $db) {
        	$in[$v] = $db->qt($v);
        });
        $sql = 'UPDATE `' . self::config()->table->name . '`
                SET `parent_id` = 0
                WHERE `id` IN (' . join(', ', $in) . ')';
        $db->query($sql);
        return $keys;
    }
}