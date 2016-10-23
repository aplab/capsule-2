<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 25.01.2014 2:03:51 YEKT 2014                                              |
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

use Capsule\DataModel\DataModel;
use Capsule\Db\Db;
use Capsule\Exception;
/**
 * Preferences.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Preferences extends DataModel
{
    /**
     * Возвращает объект предпочтений
     *
     * @param User $user
     * @return self
     */
    public static function getInstance(User $user = null) {
        if (is_null($user)) {
            $id = 0;
        } else {
            $id = $user->get('id', 0);
        }
        $class = get_called_class();
        if (!isset(self::$cache[$class][$id])) {
            self::$cache[$class][$id] = self::load($id);
        }
        return self::$cache[$class][$id];
    }

    /**
     * Load item from DB
     *
     * @param int $id
     * @return self
     */
    private static function load($id) {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $sql = 'SELECT * FROM `' . $table . '`
                WHERE `user_id` = ' . $db->qt($id);
        $ret = new static;
        $row = $db->query($sql)->fetch_assoc_first();
        if (is_array($row)) {
            $data = @unserialize($row['data']);
            if (is_array($data)) {
                $ret->data = $data;
            }
        }
        $ret->data['userId'] = $id;
        return $ret;
    }

    /**
     * Запрещает изменение userId
     *
     * @throws Exception
     */
    protected function setUserId($value, $name) {
        $msg = 'Cannot set readonly property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * Сохранить настройки пользователя перед завершением.
     *
     * @param void
     * @return void
     */
    public function __destruct() {
        $this->store();
    }

    /**
     * Сохраняет настройки перед удалением.
     *
     * @param void
     * @return void
     */
    public function store() {
        $db = Db::getInstance();
        $table = self::config()->table->name;
        $data = serialize($this->data);
        $user_id = $this->data['userId'];
        $sql = 'INSERT INTO `' . $table . '`
                SET `user_id` = ' . $db->qt($user_id) . ',
                    `data` = ' . $db->qt($data) . '
                ON DUPLICATE KEY UPDATE
                    `data` = ' . $db->qt($data);
        $db->query($sql);
    }

    /**
     * (non-PHPdoc)
     * @see \Capsule\DataModel\DataModel::__set()
     */
    public function __set($name, $value) {
        if ($value instanceof $this) {
            $msg = 'Can not refer to the object of their same class';
            throw new Exception($msg);
        }
        parent::__set($name, $value);
    }

    /**
     * Reset user settings except userId
     */
    public function reset() {
        $user_id = $this->data['userId'];
        $this->data = array();
        $this->data['userId'] = $user_id;
    }
}
