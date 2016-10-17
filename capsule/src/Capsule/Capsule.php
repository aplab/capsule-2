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


class Capsule implements \Serializable
{
    /**
     * @var Capsule
     */
    private static $instance;

    /**
     * @var string
     */
    private $systemRoot;

    /**
     * @return string
     */
    public function getSystemRoot()
    {
        return $this->systemRoot;
    }

    /**
     * @var string
     */
    private $startTime;

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
     * @throws Exception
     */
    private function __construct($document_root)
    {
        $this->startTime = microtime();
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', true);
        $this->systemRoot = dirname(__DIR__, 2);
        require_once __DIR__ . '/Core/Singleton.php';
        require_once __DIR__ . '/Core/Autoload.php';
        require_once __DIR__ . '/Core/global_functions.php';
        return;

        $this->data['alreadyRunning'] = false;
        $this->data[self::DIR_LIB] = $this->_normalizePath(dirname(__DIR__));
        $this->data['systemRoot'] = dirname($this->lib);
        $this->data[self::DIR_CFG] = $this->systemRoot . '/' . self::DIR_CFG;
        $this->data[self::DIR_EXT] = $this->systemRoot . '/' . self::DIR_EXT;
        $this->data[self::DIR_TMP] = $this->systemRoot . '/' . self::DIR_TMP;
        $this->data[self::DIR_VAR] = $this->systemRoot . '/' . self::DIR_VAR;
        $this->data[self::DIR_BIN] = $this->systemRoot . '/' . self::DIR_BIN;
        $this->data[self::DIR_TPL] = $this->systemRoot . '/' . self::DIR_TPL;
        include 'Exception.php';
        include $this->{self::DIR_LIB} . '/Capsule/Core/Exception.php';
        if (PHP_MAJOR_VERSION < 5 or PHP_MINOR_VERSION < 4 or PHP_RELEASE_VERSION < 3) {
            $msg = 'Supported php version 5.4.3+';
            throw new Core\Exception($msg);
        }
        include $this->{self::DIR_LIB} . '/Capsule/Core/Singleton.php';
        include $this->{self::DIR_LIB} . '/Capsule/Core/Autoload.php';
        include $this->{self::DIR_LIB} . '/Capsule/Core/global_functions.php';
        if (is_null($document_root)) {
            if (isset($_SERVER['DOCUMENT_ROOT'])) {
                $this->data['documentRoot'] = $_SERVER['DOCUMENT_ROOT'];
            } else {
                $msg = 'Cannot be determined DOCUMENT_ROOT';
                throw new Core\Exception($msg);
            }
        } else {
            $this->data['documentRoot'] = $document_root;
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
    private function _normalizePath($path)
    {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', $path)), '/');
    }
}