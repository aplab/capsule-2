<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace Capsule\DataModel;

use ReflectionClass;
use Capsule\Component\Path\Path;
use Capsule\Capsule;
use Capsule\DataModel\Config\Storage;
use Capsule\DataModel\Config\Config;
use Capsule\Db\Db;
use Capsule\Core\Fn;
use PHP\Exceptionizer\Exceptionizer;

/**
 * DataModel.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class DataModel
{
    /**
     * DO NOT CHANGE!
     * config
     * used in the configuration files to refer to himself
     * используется в конфигах для ссылки на сам конфиг
     *
     * @var string
     */
    const REF_CONFIG = 'config';

    /**
     * DO NOT CHANGE!
     * config
     * used in the configuration files to refer to current section
     * используется в конфигах для ссылки на ту же секцию, в которой находится
     *
     * @var string
     */
    const REF_THIS = 'this';

    /**
     * Свойства объекта
     *
     * @var array
     */
    protected $data = array();

    /**
     * Общие свойства класса
     *
     * @var array
     */
    protected static $common = array();

    /**
     * Хранилище созданных экземпляров классов
     * Ключ - имя класса, значение - массив объектов, упорядоченный по
     * первичному ключу, либо иначе, на усмотрение модуля
     *
     * @var array
     */
    protected static $cache = array();

    /**
     * constructor
     *
     * @param void
     * @throws Exception
     */
    protected function __construct() {}

    /**
     * Возвращает значение свойства. Если свойство не определено или
     * отсутствует, то генерируется исключение.
     *
     * @param  string
     * @throws Exception
     * @return mixed
     */
    public function __get($name)
    {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $properties = $this->config()->get('properties', new \stdClass());
        if (isset($properties->$name)) {
            $msg = 'Undefined property: ';
        } else {
            $msg = 'Unknown property: ';
        }
        $msg .= get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * Возвращает значение свойства или значение по умолчанию, если свойство
     * не определено.
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * Обрабатывает изменение значения свойства.
     *
     * @param  string $name
     * @param  mixed $value
     * @throws Exception
     * @return self
     */
    public function __set($name, $value)
    {
        $properties = $this->config()->get('properties', new \stdClass);
        if (isset($properties->$name)) {
            $property = $this->config()->properties->$name;
            $validator = $property->get('validator', null);
            if ($validator) {
                if ($validator->isValid($value)) {
                    $value = $validator->getClean();
                } else {
                    $msg = 'Invalid value: ' . get_class($this) . '::$' . $name;
                    throw new Exception($msg);
                }
            }
        }
        $setter = self::_setter($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value, $name);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Returns module configuration object
     *
     * @param void
     * @return Config
     */
    final public static function config()
    {
        $c = get_called_class();
        if (!isset(self::$common[$c][self::REF_CONFIG])) {
            self::$common[$c][self::REF_CONFIG] = self::_loadConfig();
        }
        return self::$common[$c][self::REF_CONFIG];
    }

    /**
     * Load module configuration object
     *
     * @return array
     */
    protected static function _loadConfig()
    {
        $class = get_called_class();
        if (!Storage::getInstance()->exists($class)) {
            $data = self::_configData();
            /**
             * Post-processing values like __CLASS__, "config.some_value.another_value"
             */
            array_walk_recursive($data, function (& $v) use ($data, $class) {
                if (false !== strpos($v, '__CLASS__')) {
                    $v = str_replace('__CLASS__', $class, $v);
                }
                if (!(strpos($v, '.'))) {
                    return;
                }
                $pcs = explode('.', $v);
                $pcs = array_filter($pcs, 'trim');
                if (sizeof($pcs) < 2) {
                    return;
                }
                if (self::REF_CONFIG !== array_shift($pcs)) {
                    return;
                }
                $tmp = $data;
                foreach ($pcs as $i) {
                    if (!array_key_exists($i, $tmp)) {
                        return;
                    }
                    $tmp = $tmp[$i];
                }
                $v = $tmp;
            });
            Storage::getInstance()->set($class, new Config($data));
        }
        return Storage::getInstance()->get($class);
    }

    /**
     * Returns an array of data to create a configuration object
     *
     * @return array
     */
    protected static function _configData()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = self::_buildConfigData();
        }
        return self::$common[$c][$f];
    }

    /**
     * Собирает и возвращает данные конфига с учетом наследования.
     *
     * @return array
     */
    protected static function _buildConfigData()
    {
        $class = get_called_class();

        $default_associated_table = Inflector::getInstance()->getAssociatedTable($class);
        $fragment = self::_configDataFragment();// загрузить фрагмент
        if (isset($fragment['table'])) {// есть секция table, значит работает с таблицей
            if (!isset($fragment['table']['name'])) {// имя не задано, ставим значение по умолчанию
                $fragment['table']['name'] = $default_associated_table;
            }
        }

        $parent_data = array();// родительский класс
        $parent_class = get_parent_class($class);
        if ($parent_class) {// данные родительского класса по такому же принципу
            /**
             * @var DataModel $parent_class
             */
            $parent_data = $parent_class::_configData();
        }

        // Получаем из фрагмента конфига только переопределенные параметры
        $diff = Fn::array_diff_assoc_recursive($fragment, $parent_data);
        if (isset($diff['table']['name']) && $diff['table']['name'] === $default_associated_table) {
            // название таблицы было переопределено по сравнению с родительским классом
            // переопределенное значение равно значению по умолчанию для этого класса
            // значение по умолчанию не прописываем
            unset($diff['table']['name']);
        }

        if (Fn::array_diff_assoc_recursive($fragment, $diff)) {
            // Если в фрагменте были не переопределенные параметры, они удалились.
            // Если получившийся в результате фрагмент изменился по сравнению с исходным, сохранить его.
            // Перезапись файла
            self::_saveConfigFragment($diff);
        }

        if (isset($diff['table'])) {
            /**
             * Возможно, неочевидное поведение:
             * Если в конфиге есть секция table, значит у модуля должна быть
             * своя таблица. Если такой секции нет, то модуль работает с
             * таблицей модуля-предка, если такой есть; Или не может работать с
             * таблицей вообще.
             */
            if (!isset($diff['table']['name'])) {
                /**
                 * Если имя таблицы не задано вручную, то оно генерируется
                 * автоматически на основе полного имени класса.
                 */
                $diff['table']['name'] = Inflector::getInstance()->getAssociatedTable($class);
            }
        }

        // Наложение переопределенных данных на родительский
        return array_replace_recursive($parent_data, $diff);
    }

    /**
     * Prepare full config file for developer
     *
     * @return bool
     * @throws \Capsule\DataModel\Exception
     * @param void
     */
    public static function _configSetEditMode()
    {
        $data = self::_buildConfigData();

        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode($data, $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }

        $path = self::_configLocation();
        self::_createConfigFile();
        if (false === file_put_contents($path, $json, LOCK_EX)) {
            $msg = 'Unable to make configuration file: ' . $path;
            throw new Exception($msg);
        }
        return true;
    }

    /**
     * Применить измененный конфиг (удаляет старый кэш и перечитывает)
     *
     * @param void
     */
    public static function _configApply()
    {
        $c = get_called_class();
        self::_configClearCache();
        unset(self::$common[$c]);
        self::config();
    }

    /**
     * Очищает кэш конфига
     *
     * @param void
     * @return void
     */
    public static function _configClearCache()
    {
        $class = get_called_class();
        $storage = Storage::getInstance();
        if ($storage->exists($class)) {
            $storage->drop($class);
        }
    }

    /**
     * Возвращает только разницу в конфигурации по сравнению с прямым предком.
     * (Только то, что было переопределено)
     *
     * @param string|null $class
     * @return array
     */
    public static function _configDataFragmentDiff($class = null)
    {
        $class = $class ?: get_called_class();
        $data = self::_configDataFragment($class);
        $parent_data = array();
        $parent_class = get_parent_class($class);
        if ($parent_class) {
            $parent_data = self::_configData($parent_class);
        }
        // Получаем из фрагмента конфига только переопределенные параметры
        return Fn::array_diff_assoc_recursive($data, $parent_data);
    }

    /**
     * Возвращает только разницу в конфигурации по сравнению с прямым предком.
     * (Только то, что было переопределено)
     * В формате JSON
     *
     * @param string|null $class
     * @return string
     */
    public static function _configDataFragmentDiffJson($class = null)
    {
        $class = $class ?: get_called_class();
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        return json_encode(self::_configDataFragmentDiff($class), $opt);
    }

    /**
     * Возвращает данные конфига с учетом наследования в формате json.
     *
     * @param string|null $class
     * @throws Exception
     * @return string
     */
    public static function _configDataJson($class = null)
    {
        $class = $class ?: get_called_class();
        if (!isset(self::$common[$class][__FUNCTION__])) {
            $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
            self::$common[$class][__FUNCTION__] = json_encode(self::_configData($class), $opt);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new Exception(json_last_error_msg());
            }
        }
        return self::$common[$class][__FUNCTION__];
    }

    /**
     * Сохраняет фрагмент конфигурационного файла
     *
     * @param array $data
     * @throws Exception
     */
    protected static function _saveConfigFragment(array $data)
    {
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode($data, $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }
        $path = self::_configLocation();
        if (false === file_put_contents($path, $json, LOCK_EX)) {
            $msg = 'Unable to write diff config fragment';
            throw new Exception($msg);
        }
    }

    /**
     * Загружает конфигурационный файл модуля (фрагмент).
     * Возвращает прочтенные данные или пустой массив, если файл отсутствует.
     * WARNING! Файл никуда не кешируется и читается заново при каждом вызове.
     * Используйте _configDataFragment вместо _loadConfigDataFragment везде, где
     * это возможно.
     *
     * @param string|null $class
     * @return array
     * @throws \Capsule\DataModel\Exception
     */
    protected static function _loadConfigDataFragment($class = null)
    {
        $class = $class ?: get_called_class();
        $path = self::_configLocation($class);
        if (!file_exists($path)) {
            self::_createConfigFile();
            return array();
        }
        $content = file_get_contents($path);
        if (false === $content) {
            $msg = 'Unable to read configuration file';
            throw new Exception($msg);
        }
        $json = trim($content);
        if (!strlen($json)) {
            return array();
        }
        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg() . ' ' . $path);
        }
        if (!is_array($data)) {
            return array();
        }
        return $data;
    }

    /**
     * Возвращает фрагмент конфигурационных данных, переопределенных в текущей
     * модели.
     *
     * @return array
     */
    public static function _configDataFragment()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = $c::_loadConfigDataFragment();
        }
        return self::$common[$c][$f];
    }

    /**
     * Returns path to module config
     *
     * @param string|null $class
     * @return string
     */
    public static function _configLocation()
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = new Path(
                Capsule::getInstance()->systemRoot,
                Capsule::DIR_CONFIG,
                $c . '.json');
        }
        return self::$common[$c][$f];
    }

    /**
     * Physically create configuration file if not exists
     *
     * @param void
     * @return boolean
     * @throws \Capsule\DataModel\Exception
     */
    public static function _createConfigFile()
    {
        $path = self::_configLocation();
        if (file_exists($path)) {
            return true;
        }
        $dir = dirname($path);
        if (!is_dir($dir)) {
            $is_dir = mkdir($dir, 0700, true);
            if (!$is_dir || !is_dir($dir)) {
                $msg = 'Unable to create config directory: ' . $dir;
                throw new Exception($msg);
            }
        }
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode(array(), $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }
        if (false === file_put_contents($path, $json, LOCK_EX)) {
            $msg = 'Unable to create configuration file: ' . $path;
            throw new Exception($msg);
        }
        return true;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $method = 'isset' . ucfirst($name);
        if (in_array($method, self::_listMethods())) {
            return $this->$method($name);
        }
        return array_key_exists($name, $this->data);
    }

    /**
     * unset() overloading
     *
     * @param  string $name
     * @return void
     * @throws Exception
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Возвращает ReflectionClass для класса.
     *
     * @param string|null $class
     * @return ReflectionClass
     */
    final protected static function _reflectionClass($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = new ReflectionClass($c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает root directory для класса.
     *
     * @param string|null $class
     * @return string
     */
    final protected static function _rootDir($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = str_replace('\\', '/', dirname(static::_reflectionClass($c)->getFileName()));
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает список методов класса с учетом регистра.
     *
     * @param string $class
     * @return array
     */
    protected static function _listMethods($class = null)
    {
        $c = $class ?: get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f])) {
            self::$common[$c][$f] = get_class_methods($c);
        }
        return self::$common[$c][$f];
    }

    /**
     * Возвращает getter с учетом регистра.
     *
     * @param string $name
     * @return string|false
     */
    protected static function _getter($name)
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f][$name])) {
            $getter = 'get' . ucfirst($name);
            self::$common[$c][$f][$name] =
                in_array($getter, self::_listMethods()) ? $getter : false;
        }
        return self::$common[$c][$f][$name];
    }

    /**
     * Возвращает setter с учетом регистра.
     *
     * @param string $name
     * @return string|false
     */
    protected static function _setter($name)
    {
        $c = get_called_class();
        $f = __FUNCTION__;
        if (!isset(self::$common[$c][$f][$name])) {
            $setter = 'set' . ucfirst($name);
            self::$common[$c][$f][$name] = in_array($setter, self::_listMethods()) ? $setter : false;
        }
        return self::$common[$c][$f][$name];
    }

    /**
     * Возвращает все объекты из связанной таблицы
     *
     * @param void
     * @return self
     */
    public static function all()
    {
        $sql = 'SELECT * FROM `' . self::config()->table->name . '`';
        return static::populate(Db::getInstance()->query($sql));
    }

    /**
     * Возвращает количество объектов из связанной таблицы
     *
     * @param void
     * @return int
     */
    public static function number()
    {
        $sql = 'SELECT COUNT(*) FROM `' . self::config()->table->name . '`';
        return Db::getInstance()->query($sql)->fetch_one();
    }

    /**
     * Returns pages number
     *
     * @param int|number $items_per_page
     * @return array
     */
    public static function pages($items_per_page = 10)
    {
        $c = self::number();
        if (!$c) {
            return array();
        }
        return range(1, ceil($c / $items_per_page));
    }

    /**
     * common key generator
     *
     * @param string $__FUNCTION__
     * @return string
     */
    protected static function ck($__FUNCTION__ = null)
    {
        $c = get_called_class();
        $e = new Exceptionizer;
        if (is_null($__FUNCTION__)) {
            $f = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS ^ DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
            $f = $f[1]['function'];
        } else {
            $f = $__FUNCTION__;
        }
        return Fn::concat_ws_ne('::', $c, $f);
    }

    /**
     * Возвращает фрагмент конфига с конфигурацией индексов, которые фактически определены в таблице базы данных.
     * По умолчанию возвращает данные для связанной с модулем таблицы.
     * Можно переопределить имя базы данных или таблицы для получения конфигурации любой таблицы.
     *
     * @param null|string $table_name
     * @param null|string $table_schema
     * @return string
     * @throws \Capsule\DataModel\Exception
     */
    public static function _realIndexConfigData($table_name = null, $table_schema = null)
    {
        $class = get_called_class();
        $db = Db::getInstance();
        if (is_null($table_schema)) {
            $table_schema = $db->config->dbname;
        }
        if (is_null($table_name)) {
            $table_name = $class::config()->table->name;
        }
        $sql = 'SELECT
                    `TABLE_CATALOG`,
                    `TABLE_SCHEMA`,
                    `TABLE_NAME`,
                    `NON_UNIQUE`,
                    `INDEX_SCHEMA`,
                    `INDEX_NAME`,
                    `SEQ_IN_INDEX`,
                    `COLUMN_NAME`,
                    `COLLATION`,
                    `CARDINALITY`, 
                    `SUB_PART`,
                    `PACKED`,
                    `NULLABLE`,
                    `INDEX_TYPE`,
                    `COMMENT`,
                    `INDEX_COMMENT`,
                    if ("PRIMARY" = `INDEX_NAME`, 0, 1 + `NON_UNIQUE`) AS `PREORDER_CUSTOM`
                FROM `information_schema`.`STATISTICS`
                WHERE `TABLE_SCHEMA` = ' . $db->qt($table_schema) . '
                AND `TABLE_NAME` = ' . $db->qt($table_name) . '
                ORDER BY `TABLE_SCHEMA`, `TABLE_NAME`, `PREORDER_CUSTOM`, `INDEX_NAME`, `SEQ_IN_INDEX`';

        $data = $db->query($sql)->fetch_object_all();
        $tmp = array();
        foreach ($data as $data_item) {
            $index_name = $data_item->INDEX_NAME;
            if ('PRIMARY' === $index_name) {
                $index_name = 'primaryKey';
            }
            if (!isset($tmp[$index_name])) {
                $tmp[$index_name] = array();
            }
            if (preg_match('/^primary/i', $index_name)) {
                $tmp[$index_name]['type'] = 'primaryKey';
            } elseif ('0' === $data_item->NON_UNIQUE) {
                $tmp[$index_name]['type'] = 'uniqueKey';
            } elseif ('FULLTEXT' === $data_item->INDEX_TYPE) {
                $tmp[$index_name]['type'] = 'fulltextKey';
            }
            if (!isset($tmp[$index_name]['fields'])) {
                $tmp[$index_name]['fields'] = array();
            }
            $tmp[$index_name]['fields'][$data_item->COLUMN_NAME] = array();
            if (preg_match('/\\d+/', $data_item->SUB_PART, $m)) {
                $length = $m[0];
                settype($length, 'integer');
                $tmp[$index_name]['fields'][$data_item->COLUMN_NAME]['length'] = $length;
            }
        }
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode($tmp, $opt);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }
        return $json;
    }
}