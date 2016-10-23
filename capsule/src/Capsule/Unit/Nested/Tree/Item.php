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

namespace Capsule\Unit\Nested\Tree;

use PHP\Exceptionizer\Exceptionizer;
use Capsule\Db\Db;
use Capsule\Core\Fn;
use Capsule\Unit\Nested\NamedItem;
use Capsule\Model\IdBased;
use Capsule\Exception;
/**
 * Item.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Item extends NamedItem
{
    use \Capsule\Traits\sortOrder;
    
    /**
     * Возвращает дерево
     *
     * @param void
     * @return array
     */
    public static function treeByContainer($container_id) {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `container_id` = ' . $db->qt($container_id) . '
	            ORDER BY `parent_id`, ' . $db->bq(static::$key);
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
     * Возвращает список options для select
     *
     * @param void
     * @return array
     */
    public static function optionsDataListByContainer($container_id) {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $class = get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__][$container_id])) {
            $tmp = $class::treeByContainer($container_id);
            array_walk($tmp, function($v, $k) use (& $tmp) {
                $tmp[$k] = array(
                    'value' => $v->id,
                    'text' => str_repeat('. ', $v->level) . $v->name,
                    'selectd' => false
                );
            });
            self::$common[$class][__FUNCTION__][$container_id] = $tmp;
        }
        return self::$common[$class][__FUNCTION__][$container_id];
    }
    
    /**
     * Восстанавливает элементы, у которых по какой-либо причине не найден
     * родительский элемент, или возникла петля, циклическая ссылка, или не 
     * найден контейнер, или всё сразу, переводя их на нулевой уровень без 
     * контейнера, в результате чего они становятся видны в списке.
     *
     * Возвращает количество таких элементов.
     *
     * @param void
     * @return int
     */
    public static function repair() {
        $e = new Exceptionizer();
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . self::config()->table->name;
        $all = static::populate($db->query($sql));
        $tmp3d = self::to3d($all);
        $good = array();
        $container_class = Fn::create_classname(static::config()->container);
        $container_ids = array_flip($container_class::keys());
        // Заранее отфильтровать с несуществующими контейнерами
        $tmp3d = array_intersect_key($tmp3d, $container_ids);
        $filter = function($tmp2d, $from_key = 0) use (& $filter, & $good) {
            if (!isset($tmp2d[$from_key])) return;
            foreach ($tmp2d[$from_key] as $k => $v) {
                $good[$k] = $v;
                if (isset($tmp2d[$k])) $filter($tmp2d, $k);
            }
        };
        foreach ($tmp3d as $container_id => $tmp2d) $filter($tmp2d);
        $corrupted = array_diff_key($all, $good);
        if (empty($corrupted)) return array();
        $in = array();
        $keys = array_keys($corrupted);
        array_walk($keys, function($v, $k) use (& $in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'UPDATE `' . self::config()->table->name . '`
                SET `container_id` = 0, `parent_id` = 0
                WHERE `id` IN (' . join(', ', $in) . ')';
        $db->query($sql);
        return $db->affected_rows;
    }
    
    /**
     * Упорядочивает переданные объекты в виде трехмерного массива
     * [parent_id][id] = object
     *
     * @param mixed
     * @return array
     */
    public static function to3d() {
        if (!func_num_args()) return array();
        $tmp = array();
        $a = func_get_args();
        array_walk_recursive($a, function($v, $k) use (& $tmp) {
            if ($v instanceof self) {
                $container_id = $v->containerId;
                $parent_id = $v->parentId;
                $id = $v->{static::$key};
                if ($id) $tmp[$container_id][$parent_id][$id] = $v;
            }
        });
        return $tmp;
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
     * Возвращает количество элементов у которых нет привязки к контейнеру
     *
     * @param void
     * @return array
     */
    public static function numberWithoutContainer() {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        $in = array();
        $db = Db::getInstance();
        array_walk($keys, function($v, $k) use(&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
	            WHERE `container_id` NOT IN(' . join(', ', $in) . ')';
        return $db->query($sql)->fetch_one();
    }
    
    /**
     * Возвращает массив номеров страниц элементов, 
     * у которых нет привязки к контейнеру
     *
     * @param number $items_per_page
     * @return array
     */
    public static function pagesWithoutContainer($items_per_page = 10) {
        $c = self::numberWithoutContainer();
        if (!$c) return array();
        return range(1, ceil($c / $items_per_page));
    }
    
    /**
     * Возвращает страницу элементов у которых нет привязки к контейнеру
     *
     * @param void
     * @return array
     */
    public static function pageWithoutContainer($page_number = 1, $items_per_page = 10) {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        $in = array();
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        array_walk($keys, function($v, $k) use(&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `container_id` NOT IN(' . join(', ', $in) . ')
                ORDER BY ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }
    
    /**
     * Возвращает количество элементов у которых есть привязка к контейнеру
     *
     * @param void
     * @return array
     */
    public static function numberWithContainer() {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        $in = array();
        $db = Db::getInstance();
        array_walk($keys, function($v, $k) use(&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
	            WHERE `container_id` IN(' . join(', ', $in) . ')';
        return $db->query($sql)->fetch_one();
    }
    
    /**
     * Возвращает массив номеров страниц элементов, 
     * у которых есть привязка к контейнеру
     *
     * @param number $items_per_page
     * @return array
     */
    public static function pagesWithContainer($items_per_page = 10) {
        $c = self::numberWithContainer();
        if (!$c) return array();
        return range(1, ceil($c / $items_per_page));
    }
    
    /**
     * Возвращает страницу элементов, 
     * у которых есть привязка к контейнеру
     *
     * @param void
     * @return array
     */
    public static function pageWithContainer($page_number = 1, $items_per_page = 10) {
        $container_class = Fn::create_classname(static::config()->container);
        $keys = $container_class::keys();
        $in = array();
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        array_walk($keys, function($v, $k) use(&$in, $db) {
            $in[$v] = $db->qt($v);
        });
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `container_id` IN(' . join(', ', $in) . ')
                ORDER BY ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        return static::populate($db->query($sql));
    }
    
    /**
     * Возвращает количество корневых элементов в контейнере.
     * Для дерева считаем количество элементов на странице по корням.
     * 
     * Корневым элементом называется такой элемент, у которого id родителя = 0
     *
     * @param int $container_id
     * @return int
     */
    public static function numberRootsByContainer($container_id) {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return 0;
        $db = Db::getInstance();
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`
	            WHERE `container_id` = ' . $db->qt($container_id) . '
                AND `parent_id` = 0';
        return $db->query($sql)->fetch_one();
    }
    
    /**
     * Возвращает массив номеров страниц элементов заданного контейнера.
     * Для дерева считаем количество элементов на странице по корням.
     *
     * @param number $items_per_page
     * @return array
     */
    public static function pagesByContainer($container_id, $items_per_page = 10) {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $c = self::numberRootsByContainer($container_id);
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
    public static function pageByContainer($container_id, $page_number = 1, $items_per_page = 10) {
        $e = new Exceptionizer();
        settype($container_id, 'string');
        if (!ctype_digit($container_id)) return array();
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `parent_id` = 0
                AND `container_id` = ' . $db->qt($container_id) . '
                ORDER BY `sort_order`, ' . $db->bq(static::$key) . '
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        $roots = static::populate($db->query($sql));
        $tmp = array();
        $children = $roots;
        $level = 0;
        while ($children) {
            $children_filtered = array();
            array_walk($children, function($v, $k) use ($level, $container_id, &$children_filtered) {
                if ($v->containerId == $container_id) {
                    $v->level = $level;
                    $children_filtered[$k] = $v; 
                }
            });
            $tmp += $children_filtered;
            $children = self::childrenOf($children_filtered);
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
                ORDER BY ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }
    
    /**
     * Задает или возвращает объект контейнер или null
     * 
     * @param void
     * @return IdBased
     */
    public function container() {
        // Класс контейнера
        $container_classname = Fn::cc($this->config()->container);
        if (!func_num_args()) {
            // Без аргументов, возвращает текущий контейнер
            return $container_classname::id($this->containerId);
        }
        $db = Db::getInstance();
        // Класс элемента списка
        $classname = get_class($this);
        $destination = func_get_arg(0);
        if ($destination instanceof $container_classname) {
            $container_id = $this->get('containerId');
            $destination_container_id = $destination->id;
            /**
             * тот-же самый (same) контейнер, делать ничего не надо
             */
            if ($destination_container_id == $container_id) {
                return $destination;
            }
            /**
             * другой контейнер
             */
            $id = $this->get('id');
            if ($id) {
                // получить всю ветку
                $branch = $this->branch(); // у всей ветки меняется контейнер
                /** ключи соответствуют идентификаторам, @see branch() */
                $in = array_keys($branch);
                $in[] = $id;
                array_walk($in, function(& $v, $k) use ($db) {
                    $v = $db->qt($v);
                });
                $sql = 'UPDATE `' . self::config()->table->name . '`
                    	SET `parent_id` = IF(`id` = ' . $db->qt($id) . ', 0, `parent_id`),
                    		`container_id` = ' . $db->qt($destination_container_id) . '
                        WHERE `id` IN(' . join(', ', $in) . ')';
                $db->query($sql);
                // Можно изменять protected - свойства, находясь внутри класса
                $this->data['parentId'] = '0';
                $this->data['containerId'] = $destination_container_id;
                foreach ($branch as $item) {
                    $item->data['containerId'] = $destination_container_id;
                }
            } else {
                // Если объект новый, то у него нет id и нет потомков (ветки)
                $this->data['parentId'] = '0';
                $this->data['containerId'] = $destination_container_id;
            }
            return $destination;
        }
        /**
         * Перемещение на нулевой уровень никакого контейнера
         */
        $id = $this->get('id');
        if ($id) {
            $branch = $this->branch(); // у всей ветки меняется контейнер
            $in = array_keys($branch);
            $in[] = $id;
            array_walk($in, function(& $v, $k) use ($db) {
                $v = $db->qt($v);
            });
            $sql = 'UPDATE `' . self::config()->table->name . '`
                    	SET `parent_id` = 0, -- Разрушаем структуру ветки, потому что нет контейнера - нет и дерева
                    		`container_id` = 0
                        WHERE `id` IN(' . join(', ', $in) . ')';
            $db->query($sql);
            $this->data['parentId'] = '0';
            $this->data['containerId'] = '0';
            foreach ($branch as $item) {
                $item->data['containerId'] = '0';
                // Разрушаем структуру ветки, потому что нет контейнера - нет и дерева
                $item->data['parentId'] = '0';
            }
        } else {
            // Если объект новый, то у него нет id и нет потомков (ветки)
            $this->data['parentId'] = '0';
            $this->data['containerId'] = '0';
        }
        return null;
    }
    
    /**
     * Smart set container id
     * 
     * @param unknown $v value
     * @param unknown $n property name
     */
    protected function setContainerId($v, $n) {
        $c = Fn::create_classname($this->config()->container);
        $this->container($c::id($v));
    }
    
    /**
     * Smart set parent id
     *
     * @param unknown $v value
     * @param unknown $n property name
     */
    protected function setParentId($v, $n) {
        $this->parentElement($this->id($v));
    }
    
    /**
     * Задает или возвращает объект - родитель или null
     *
     * @param void
     * @return self
     */
    public function parentElement() {
        $db = Db::getInstance();
        if (!func_num_args()) {
            // Без аргументов просто возвращаем текущий родитель
            $sql = 'SELECT * FROM `' . self::config()->table->name . '`
    	            WHERE `container_id` = ' . $db->qt($this->containerId) . '
                    AND `id` = ' . $db->qt($this->parentId);
            $objects = self::populate($db->query($sql));
            // array_shift returns NULL if array is empty
            return array_shift($objects);
        }
        // Класс элемента списка
        $classname = get_class($this);
        $destination = func_get_arg(0);
        if ($destination instanceof $classname) {
            $container_id = $this->containerId;
            $destination_container_id = $destination->containerId;
            $destination_id = $destination->id;
            /**
             * У объекта - назначения тот-же контейнер
             */
            $id = $this->get('id');
            if ($id) {
                if ($container_id === $destination_container_id) {
                    // Нельзя переместить объект в самого себя.
                    if ($destination_id === $id) {
                        $message = 'It is impossible to move object into itself.';
                        throw new \Exception($message);
                    }
                    // Нельзя переместить объект в своего же потомка.
                    if ($this->isAncestorOf($destination)) {
                        $message = 'It is impossible to move object into his descendant.';
                        throw new \Exception($message);
                    }
                    $parent_id = $this->parentId;
                    // Остается без изменений
                    if ($parent_id === $destination_id) {
                        return $destination;
                    }
                    $this->data['parentId'] = $destination_id;
                    return $destination;
                }
            } else {
                $this->data['parentId'] = $destination_id;
                return $destination;
            }
            /**
             * У объекта - назначения другой контейнер
             */
            if ($id) {
                // Нельзя переместить объект в самого себя.
                if ($destination_id === $id) {
                    $message = 'It is impossible to move object into itself.';
                    throw new \Exception($message);
                }
                // Нельзя переместить объект в своего же потомка.
                if ($this->isAncestorOf($destination)) {
                    $message = 'It is impossible to move object into his descendant.';
                    throw new \Exception($message);
                }
                $parent_id = $this->parentId;
                // Остается без изменений
                if ($parent_id === $destination_id) {
                    return $destination;
                }
                $branch = $this->branch(); // у всей ветки меняется контейнер
                $id = $this->id;
                $in = array_keys($branch);
                $in[] = $id;
                array_walk($in, function(& $v, $k) use ($db) {
                    $v = $db->qt($v);
                });
                $sql = 'UPDATE `' . self::config()->table->name . '`
                    	SET `parent_id` = IF(`id` = ' . $db->qt($id) . ', ' . $db->qt($destination_id) . ', `parent_id`),
                    		`container_id` = ' . $db->qt($destination_container_id) . '
                        WHERE `id` IN(' . join(', ', $in) . ')';
                $db->query($sql);
                $this->data['parentId'] = $destination_id;
                $this->data['containerId'] = $destination_container_id;
                foreach ($branch as $item) {
                    $item->data['containerId'] = $destination_container_id;
                }
                return $destination;
            } else {
                $this->data['parentId'] = $destination_id;
                $this->data['containerId'] = $destination_container_id;
            }
        }
        /**
         * перемещение на нулевой уровень. Саму ветку не трогаем, потому что 
         * не меняется ни контейнер ни родитель ни у кого в ветке, кроме 
         * текущего элемента
         */
        $this->data['parentId'] = '0';
        return null;
    }
    
    /**
     * Проверяет, является ли объект потомком данного объекта
     *
     * @param self $item
     * @return boolean
     */
    public function isDescendantOf($item) {
        $c = get_class($this);
        if (!($item instanceof $c)) return false;
        if ($item === $this) return false;
        $parent = $this->parentElement();
        while ($parent instanceof $c) {
            if ($parent->containerId !== $this->containerId) {
                $msg = 'Corrupted branch detected: ' . $c . '::' . $this->id;
                throw new Exception($msg);
            }
            if ($parent === $item) return true;
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
        $c = get_class($this);
        if (!($item instanceof $c)) return false;
        if ($item === $this) return false;
        $parent = $item->parentElement();
        while ($parent instanceof $c) {
            if ($parent->containerId !== $this->containerId) {
                $msg = 'Corrupted branch detected: ' . $c . '::' . $this->id;
                throw new Exception($msg);
            }
            if ($parent === $this) return true;
            $parent = $parent->parentElement();
        }
        return false;
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
                AND `container_id` = ' . $db->qt($this->containerId) . '
                ORDER BY `sort_order`, ' . $db->bq(static::$key);
        return static::populate($db->query($sql));
    }
    
    /**
     * Returns branch from this element without this element 
     * as not ordered array.
     * 
     * @param void
     * @return array
     */
    public function branch() {
        $db = Db::getInstance();
        $c = $this->children();
        $container_id = $db->qt($this->containerId);
        $tmp = array();
        while ($c) {
            $tmp += $c;
            $in = array();
            array_walk($c, function($v, $k) use ($db, & $in) {
            	$in[] = $db->qt($k);
            });
            $sql = 'SELECT * FROM `' . self::config()->table->name . '`
	            WHERE `parent_id` IN (' . join(', ', $in) . ')
                AND `container_id` = ' . $container_id . '
                ORDER BY ' . $db->bq(static::$key);
            $c = static::populate($db->query($sql));
        }
        return $tmp;
    }
}