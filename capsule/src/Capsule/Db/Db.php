<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 20.05.2013 23:37:44 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Db;

use mysqli_sql_exception, mysqli_driver, mysqli;
use Capsule\Tools\Tools;
use Capsule\Core\Fn;
use Capsule\DataStorage\DataStorage;
use Capsule\Common\Path;
use Capsule\DataStruct\Loader;
use Capsule\Capsule;
use Capsule\Exception;
use Capsule\Loader\GeSHi;

/**
 * Db.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Config $config
 */
class Db extends mysqli
{
    /**
     * Default connection, where getInstance() called without param
     *
     * @var string
     */
    const DEFAULT_CONNECTION = 'default';

    /**
     * open connections
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * @var Config
     */
    protected static $config;

    /**
     * Debug show queries
     *
     * @var boolean
     */
    public static $debug = false;

    /**
     * object properties
     *
     * @var array
     */
    private $data = array();

    /**
     * Получить соединение.
     * Если никакого соединения нет, и конфиг не передан, то будет
     * возвращать соединение по умолчанию
     *
     * @param null $name
     * @return $this
     */
    public static function getInstance($name = null)
    {
        if (is_null($name)) {
            $name = self::DEFAULT_CONNECTION;
        }
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self(self::config()->$name);
        }
        return self::$instances[$name];
    }

    /**
     * Retrieve config
     *
     * @return mixed
     * @throws \Capsule\DataStruct\Exception
     */
    protected static function config()
    {
        $name = __FUNCTION__;
        if (!self::$$name) {
            $class = get_called_class();
            $storage = DataStorage::getInstance();
            if ($storage->exists($class)) {
                self::$$name = $storage->get($class);
                $storage->get($class);
            } else {
                $path = new Path(Capsule::getInstance()->{Capsule::DIR_CFG}, $class . '.json');
                $loader = new Loader();
                $data = $loader->loadJson($path);
                $$name = new Config($data);
                $storage->set($class, $$name);
                self::$$name = $$name;
            }
        }
        return self::$$name;
    }

    /**
     * Constructor
     *
     * @param Config|null|object $config
     */
    public function __construct(Config $config)
    {
        $driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
        $this->data['driver'] = $driver;
        $this->data['config'] = $config;
        $this->init(); // mysqli_init
        try {
            if ($config->socket) {
                $this->real_connect(
                    ($config->persistent ? 'p:' : '') . $config->host,
                    $config->username,
                    $config->passwd,
                    $config->dbname,
                    $config->port,
                    $config->socket
                );
            } else {
                $this->real_connect(
                    ($config->persistent ? 'p:' : '') . $config->host,
                    $config->username,
                    $config->passwd,
                    $config->dbname,
                    $config->port
                );
            }
        } catch (\mysqli_sql_exception $e) {
            die($e->getMessage());
        }
        $this->set_charset($config->charset);
        $this->select_db($config->dbname);
    }

    /**
     * execute query
     *
     * @param string $sql
     * @return Result
     */
    public function query($sql)
    {
        if (self::$debug) {
            $trace = debug_backtrace(null, 2);
            $trace = array_pop($trace);
            $service_info = 'sql: ' . preg_replace('/\\s{2,}/', ' ', $sql);
            if ($trace) {
                $service_info = ' line: ' . $trace['line'] . ' ' . $service_info;
                $service_info = 'file: ' . $trace['file'] . $service_info;
            }
            Tools::dump($service_info);
        }
        try {
            $this->real_query($sql);
            return new Result($this);
        } catch (mysqli_sql_exception $e) {
            $e->sql = preg_replace('/\\s{2,}/', ' ', $sql);
            $trace = $e->getTrace();
            $function = __FUNCTION__;
            $class = get_class($this);
            $break = false;
            foreach ($trace as $data_item) {
                if ($break) {
                    $e->called_from = $data_item;
                    break;
                }
                if ($function === $data_item['function'] &&
                    $class === $data_item['class']
                ) {
                    $break = true;
                }
            }
            GeSHi::getInstance();
            $geshi = new \GeSHi($sql, 'sql');
            $geshi->enable_classes(false);
            echo $geshi->parse_code();
            throw $e;
        }
    }

    /**
     * mysql_real_escape_string wrapper advanced
     *
     * @param string $string
     * @return string|false
     */
    public function es($string)
    {
        return $this->real_escape_string($string);
    }

    /**
     * mysql_real_escape_string wrapper advanced
     * It handles multi-dimensional arrays recursively.
     *
     * @param string|array $string
     * @param bool $quote
     * @param bool $double
     * @return false|string
     */
    public function qt($string, $quote = true, $double = true)
    {
        if (is_array($string)) {
            $db = $this;
            array_walk($string, function (&$v) use ($db) {
                $v = $db->qt($v);
            });
            return $string;
        }
        if ($quote) {
            if ($double) {
                return '"' . $this->real_escape_string($string) . '"';
            }
            return '\'' . $this->real_escape_string($string) . '\'';
        }
        return $this->real_escape_string($string);
    }

    /**
     * Помещает значение в обратные кавычки (backquotes)
     * Обрабатывает многомерные массивы рекурсивно.
     *
     * @param string|array $value
     * @return array|string
     */
    public function bq($value)
    {
        if (is_array($value)) {
            $db = $this;
            array_walk($value, function (&$v) use ($db) {
                $v = $db->bq($v);
            });
            return $value;
        }
        return '`' . $value . '`';
    }

    /**
     * returns property value
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * handler property value change.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $msg = get_class($this) . '::$' . $name . ' is read only';
        } else {
            $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        }
        throw new Exception($msg);
    }

    /**
     * Cloning
     *
     * @param void
     * @return void
     */
    private function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    /**
     * Возвращает конфиг текущего соединения
     *
     * @param void
     * @return Config
     */
    protected function getConfig()
    {
        return $this->data['config'];
    }

    /**
     * Возвращает драйвер текущего соединения
     *
     * @param void
     * @return mysqli_driver`:
     */
    protected function getDriver()
    {
        return $this->data['driver'];
    }

    /**
     * Возвращает список таблиц в текущей базе данных
     *
     * @param void
     * @param boolean $reload
     * @return array
     */
    protected function getListTables($reload = false)
    {
        if ($reload || (!isset($this->data['list_tables']))) {
            $this->data['list_tables'] = $this->query('SHOW TABLES')->fetch_col();
        }
        return $this->data['list_tables'];
    }

    /**
     * Возвращает список полей таблицы в текущей базе данных
     *
     * @param void
     * @return array
     */
    public function listFields($table)
    {
        if (!isset($this->data['list_fields'][$table])) {
            $this->data['list_fields'][$table] =
                $this->query('SHOW COLUMNS FROM `'
                    . $this->escape_string($table) . '`')->fetch_col();
        }
        return $this->data['list_fields'][$table];
    }

    /**
     * Возвращает флаг - существует ли в базе данных таблица
     *
     * @param string $table
     * @param boolean $reload
     * @return boolean
     */
    public function tableExists($table, $reload = false)
    {
        return in_array($table, $this->getListTables($reload));
    }

    /**
     * Возвращает true если таблица пустая. В противном случае false.
     *
     * @param string $table
     * @return boolean
     */
    public function isEmpty($table)
    {
        $sql = 'SELECT 1 FROM `' . $this->es($table) . '` LIMIT 1';
        return !$this->query($sql)->num_rows;
    }

    /**
     * @param $table
     * @param bool|false $post_check
     * @throws Exception
     */
    public function drop($table, $post_check = false)
    {
        $this->query('DROP TABLE `' . $this->es($table) . '`');
        if ($post_check && $this->tableExists($table, true)) {
            $msg = 'Unable to drop table';
            throw new Exception($msg);
        }
    }

    /**
     * @param $table
     * @param bool|false $post_check
     * @throws Exception
     */
    public function dropIfExists($table, $post_check = false)
    {
        $this->query('DROP TABLE IF EXISTS `' . $this->es($table) . '`');
        if ($post_check && $this->tableExists($table, true)) {
            $msg = 'Unable to drop table';
            throw new Exception($msg);
        }
    }

    /**
     * Удалить таблицу, если она пуста
     *
     * @param $table
     * @param bool|false $post_check
     * @throws Exception
     */
    public function dropIfEmpty($table, $post_check = false)
    {
        if ($this->isEmpty($table)) {
            $this->query('DROP TABLE IF EXISTS `' . $this->es($table) . '`');
            if ($post_check && $this->tableExists($table, true)) {
                $msg = 'Unable to drop table';
                throw new Exception($msg);
            }
        }
    }

    /**
     * Разделить строку, которая представляет собой несколько запросов, разделенных разделителем ";"
     *
     * @param string $query
     * @return array
     */
    public function splitMultiQuery($query)
    {
        $ret = array();
        $token = my_strtok($query, ";");

        while ($token) {
            $prev = $token;
            $ret[] = $prev;
            $token = my_strtok();

            if (!$token) {
                return $ret;
            }
        }

        return $ret;
    }

    /**
     * Returns current database name
     *
     * @param void
     * @return string
     */
    public function selectSchema()
    {
        return $this->query('select schema()')->fetch_one();
    }
}