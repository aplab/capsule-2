<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace Capsule;


use Capsule\Component\Utf8String;
use Capsule\Core\Autoload;
use PHP\Exceptionizer\Exceptionizer;

/**
 * Class Capsule
 * @package Capsule
 * @property string $systemRoot
 * @property string $documentRoot
 * @property string $startTime
 */
class Capsule implements \Serializable
{
    /**
     * Directories relatively systemRoot
     *
     * @var string
     */
    const DIR_CACHE = 'cache';
    const DIR_CONFIG = 'config';
    const DIR_CRON = 'cron';
    const DIR_SCRIPTS = 'scripts';
    const DIR_SRC = 'src';
    const DIR_TEMPLATES = 'templates';
    const DIR_VENDOR = 'vendor';

    /**
     * Instance
     *
     * @var Capsule
     */
    private static $instance;

    /**
     * Internal data
     *
     * @var array
     */
    private $data = array();

    /**
     * Exceptionizer
     *
     * @var Exceptionizer
     */
    private $exceptionizer;

    /**
     * @param string|null $document_root
     * @return Capsule
     */
    public static function getInstance($document_root = null)
    {
        $class = get_called_class();
        if (self::$instance instanceof $class) {
            return self::$instance;
        }
        self::$instance = new $class($document_root);
        self::$instance->init();
        return self::$instance;
    }

    /**
     * @param string $document_root
     * @throws \Exception
     */
    private function __construct($document_root)
    {
        $this->data['startTime'] = microtime();
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', true);
        if (PHP_MAJOR_VERSION < 7) {
            $msg = 'Supported php version 7+';
            throw new \RuntimeException($msg);
        }
        $this->data['systemRoot'] = dirname(__DIR__, 2);
        require_once __DIR__ . '/Core/Singleton.php';
        require_once __DIR__ . '/Core/Autoload.php';
        require_once __DIR__ . '/Core/global_functions.php';
        if (is_null($document_root)) {
            if (isset($_SERVER['DOCUMENT_ROOT'])) {
                $this->data['documentRoot'] = $_SERVER['DOCUMENT_ROOT'];
            } else {
                $msg = 'Cannot be determined DOCUMENT_ROOT';
                throw new \Exception($msg);
            }
        } else {
            $this->data['documentRoot'] = $document_root;
        }
        var_dump($this);
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

    /**
     * Prevent cloning
     *
     * @throws \BadFunctionCallException
     * @param void
     * @return void
     */
    public function __clone()
    {
        throw new \BadFunctionCallException('Clone is not allowed.');
    }

    /**
     * Normalize path
     *
     * @param string $path
     * @return string
     */
    private function normalizePath($path)
    {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', $path)), '/');
    }

    /**
     * Build path
     *
     * @return string
     */
    private function buildPath()
    {
        $tmp = func_get_args();
        return $this->normalizePath(join('/', $tmp));
    }

    /**
     * Дополнительные инициализации модулей
     *
     * @param void
     * @return void
     */
    private function init()
    {
        Autoload::getInstance();
        Utf8String::initialize();
//        date_default_timezone_set($this->config->timezoneId);
        $this->exceptionizer = new Exceptionizer;
        $vendor = $this->buildPath($this->data['systemRoot'], self::DIR_VENDOR, 'autoload.php');
        var_dump($vendor);
        require_once $vendor;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     * @throws \Exception
     */
    public function get($name, $default = null)
    {
        $getter = 'get' . ucfirst($name);
        $methods = get_class_methods($this);
        if (in_array($getter, $methods)) {
            return $getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        $methods = get_class_methods($this);
        if (in_array($getter, $methods)) {
            return $getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
}