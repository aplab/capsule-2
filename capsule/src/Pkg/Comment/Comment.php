<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 02.04.2017
 * Time: 15:32
 */

namespace Pkg\Comment;


use Capsule\Db\Db;
use Capsule\Unit\UnitTsUsr;

class Comment extends UnitTsUsr
{
    /**
     * returns the roots
     *
     * @param void
     * @return array
     */
    public static function countRoots()
    {
        $db = Db::getInstance();
        $sql = 'SELECT COUNT(*) 
                FROM `' . self::config()->table->name . '`
                WHERE `parent_id` = 0';
        return $db->query($sql)->fetch_one();
    }

    /**
     * Returns pages number
     *
     * @param int $items_per_page
     * @return array
     */
    public static function pages($items_per_page = 10)
    {
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
     * @return array
     */
    public static function page($page_number = 1, $items_per_page = 10)
    {
        $db = Db::getInstance();
        $from = $items_per_page * ($page_number - 1);
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`
                WHERE `parent_id` = 0
                ORDER BY `created` DESC, `id` DESC
                LIMIT ' . $db->es($from) . ', ' . $db->es($items_per_page);
        $roots = static::populate($db->query($sql));
        $tmp = array();
        $children = $roots;
        $level = 0;
        while ($children) {
            array_walk($children, function ($v, $k) use ($level) {
                $v->level = $level;
            });
            $tmp += $children;
            $children = self::childrenOf($children);
            $level++;
        }
        $tmp = static::to2d($tmp);
        $ret = array();
        $level = 0;
        $to_list = function ($from_key = 0) use (& $ret, $tmp, & $to_list, & $level) {
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
    public static function childrenOf()
    {
        if (!func_num_args()) return array();
        $in = array();
        $a = func_get_args();
        $db = Db::getInstance();
        array_walk_recursive($a, function ($v, $k) use (& $in, $db) {
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
                ORDER BY  `created` DESC, `id` DESC';
        return static::populate($db->query($sql));
    }

    /**
     * Упорядочивает переданные объекты в виде двумерного массива
     * [parent_id][id] = object
     *
     * @param mixed
     * @return array
     */
    public static function to2d()
    {
        if (!func_num_args()) return array();
        $tmp = array();
        $a = func_get_args();
        array_walk_recursive($a, function ($v, $k) use (& $tmp) {
            if ($v instanceof self) {
                $parent_id = $v->parentId;
                $id = $v->{static::$key};
                if ($id) {
                    $tmp[$parent_id][$id] = $v;
                }
            }
        });
        return $tmp;
    }
}