<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 12.05.2014 7:02:13 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\User;

use Capsule\Filter\Inflector;
use Capsule\Db\Db;
/**
 * Env.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Env
{
    /**
     * Internal data
     *
     * @var array
     */
    private $data = array();
    
    /**
     * Items
     *
     * @var Array
     */
    protected static $instances = array();
    
    /**
     * Shared properties
     *
     * @var array
     */
    protected static $share = array();
    
    /**
     * @param User $user
     * @return self
     */
    public static function getInstance(User $user = null) {
        if (is_null($user)) {
            $user = Auth::getInstance()->currentUser;
        }
        $id = 0;
        if (is_object($user)) {
            $id = $user->get('id', 0);
        }
        $class = get_called_class();
        if (!isset(self::$instances[$class][$id])) {
            self::$instances[$class][$id] = new static($id);
        }
        return self::$instances[$class][$id];
    }
    
    /**
     * Disable create instance directly
     *
     * @param int $id
     * @return self
     */
    final protected function __construct($id) {
        $db = Db::getInstance();
        $sql = 'SELECT * FROM `' . static::_table() . '`
                WHERE `user_id` = ' . $db->qt($id);
        $row = $db->query($sql)->fetch_assoc_first();
        if (is_array($row)) {
            $data = @unserialize($row['data']);
            if (is_array($data)) {
                $this->data = $data;
            }
        }
        $this->data['userId'] = $id;
    }
    
    /**
     * Prevents changes userId
     *
     * @throws Exception
     */
    protected function setUserId($value, $name) {
        $msg = 'Cannot set readonly property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
    
    /**
     * @param void
     * @return string
     */
    protected static function _table() {
        $class = get_called_class();
        $key = __FUNCTION__;
        if (!isset(self::$share[$class][$key])) {
            $table = Inflector::getInstance()->getAssociatedTable($class);
            $db = Db::getInstance();
            if (!$db->tableExists($table)) {
                $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (
                            `user_id` SMALLINT UNSIGNED NOT NULL,
                            `data` mediumblob NOT NULL,
                            PRIMARY KEY (`user_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
                $db->query($sql);
                if (!$db->tableExists($table, true)) {
                    $msg = 'Unable to create table ' . $table;
                    throw new \Exception($msg);
                }
            }
        }
        return $table;
    }
    
    /**
     * Save user settings before completion.
     *
     * @param void
     * @return void
     */
    public function __destruct() {
        $this->store();
    }
    
    /**
     * Save settings before deleting.
     *
     * @param void
     * @return void
     */
    public function store() {
        $db = Db::getInstance();
        $data = serialize($this->data);
        $user_id = $this->data['userId'];
        $data = $db->qt($data);
        $sql = 'INSERT INTO `' . static::_table() . '`
                SET `user_id` = ' . $db->qt($user_id) . ',
                    `data` = ' . $data . '
                ON DUPLICATE KEY UPDATE
                    `data` = ' . $data;
        $db->query($sql);
    }
    
    /**
     * Reset user settings except userId
     */
    public function clr() {
        $user_id = $this->data['userId'];
        $this->data = array();
        $this->data['userId'] = $user_id;
    }
    
    /**
     * @param mixed $name
     * @param mixed $value
     * @throws \Exception
     */
    public function __set($name, $value) {
        $this->set($name, $value);
    }
    
    /**
     * @param mixed $name
     * @param mixed $value
     * @throws \Exception
     * @return self
     */
    public function set($name, $value) {
        if ($value instanceof $this) {
            $msg = 'Can not refer to the object of their same class';
            throw new \Exception($msg);
        }
        $this->data[$this->_key($name)] = $value;
        return $this;
    }
    
    /**
     * Getter
     *
     * @param mixed $name
     * @return mixed
     */
    public function __get($name) {
        $k = $this->_key($name);
        return array_key_exists($k, $this->data) ? $this->data[$k] : null;
    }
    
    /**
     * Isset alias
     *
     * @param mixed $name
     * @return mixed
     */
    public function exists($name) {
        return array_key_exists($this->_key($name), $this->data);
    }
    
    /**
     * Isset overloading
     *
     * @param mixed $name
     * @return mixed
     */
    public function __isset($name) {
        return $this->exists($name);
    }
    
    /**
     * Getter
     *
     * @param unknown $name
     * @param string $default
     * @return Ambigous <string, multitype:>
     */
    public function get($name, $default = null) {
        $k = $this->_key($name);
        return array_key_exists($k, $this->data) ? $this->data[$k] : $default;
    }
    
    /**
     * Generate key
     *
     * @param mixed $name
     * @return string
     */
    protected function _key($name) {
        return md5(serialize($name));
    }
}