<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 21.11.2016
 * Time: 0:35
 */

namespace Capsule\User\Env;


use Capsule\Db\Db;
use Capsule\Filter\Inflector;
use Capsule\User\Auth;
use Capsule\User\User;

class Env implements \Serializable
{
    /**
     * Instance internal data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Current user id
     *
     * @var int
     */
    protected $userId = 0;

    /**
     * Instances
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Internal classes data
     *
     * @var array
     */
    protected static $internal_data = [];

    /**
     * @param User $user
     * @return self
     */
    public static function getInstance(User $user = null)
    {
        if (is_null($user)) {
            $user = Auth::getInstance()->user();
        }
        $user_id = 0;
        if (is_object($user)) {
            $user_id = $user->get('id', 0);
        }
        $class = get_called_class();
        if (!isset(self::$instances[$class][$user_id])) {
            self::$instances[$class][$user_id] = new static($user_id);
        }
        return self::$instances[$class][$user_id];
    }

    /**
     * Disable create instance directly
     *
     * @param int $id
     * @return self
     */
    final protected function __construct($id)
    {
        $db = Db::getInstance();
        $sql = 'SELECT `user_id`, `data` 
                FROM `' . static::_table() . '`
                WHERE `user_id` = ' . $db->qt($id);
        $row = $db->query($sql)->fetch_assoc_first();
        if (is_array($row)) {
            try {
                $data = unserialize($row['data']);
            } catch (\Throwable $throwable) {
                $data = [];
            }
            if (is_array($data)) {
                $this->data = $data;
            }
        }
        $this->userId = $id;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected static function _table()
    {
        $class = get_called_class();
        $key = __FUNCTION__;
        if (!isset(self::$internal_data[$class][$key])) {
            $table = Inflector::getInstance()->getAssociatedTable($class);
            $db = Db::getInstance();
            if (!$db->tableExists($table)) {
                $sql = <<<SQL

CREATE TABLE IF NOT EXISTS `$table` (
    `user_id` SMALLINT UNSIGNED NOT NULL,
    `data` mediumblob NOT NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

SQL;
                $db->query($sql);
                if (!$db->tableExists($table, true)) {
                    $msg = 'Unable to create table ' . $table;
                    throw new \Exception($msg);
                }
            }
            self::$internal_data[$class][$key] = $table;
        }
        return self::$internal_data[$class][$key];
    }

    /**
     * Save user settings before completion.
     *
     * @param void
     * @return void
     */
    public function __destruct()
    {
        $this->store();
    }

    /**
     * Save settings before deleting.
     *
     * @param void
     * @return void
     */
    public function store()
    {
        $db = Db::getInstance();
        $data = serialize($this->data);
        $data = $db->qt($data);
        $sql = 'INSERT INTO `' . static::_table() . '`
                SET `user_id` = ' . $db->qt($this->userId) . ',
                    `data` = ' . $data . '
                ON DUPLICATE KEY UPDATE
                    `data` = ' . $data;
        $db->query($sql);
    }

    /**
     * Reset user settings except userId
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        $key = self::k($name);
        if (!array_key_exists($key, $this->data)) {
            $this->data[$key] = new EnvData;
        }
        if (!($this->data[$key] instanceof EnvData)) {
            $this->data[$key] = new EnvData;
        }
        return $this->data[$key];
    }

    /**
     * Isset alias
     *
     * @param mixed $name
     * @return mixed
     */
    public function exists($name)
    {
        return array_key_exists($this->_key($name), $this->data);
    }

    /**
     * Isset overloading
     *
     * @param mixed $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->exists($name);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        throw new \Exception('direct modification not allowed');
    }

    /**
     * @param $name
     * @return string
     */
    protected static function k($name)
    {
        return md5(serialize($name));
    }

    /**
     * Уничтожить переменную
     *
     * @param string $name
     */
    public function purge($name)
    {
        $key = $this->k($name);
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
    }

    /**
     * @param void
     * @return void
     * @throws \BadFunctionCallException
     */
    public function serialize()
    {
        throw new \BadFunctionCallException('You cannot serialize this object.');
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        throw new \BadFunctionCallException('You cannot unserialize this object.');
    }
}