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
/**
 * @package Capsule
 */

namespace Capsule\Core;

use Capsule\Capsule;
/**
 * Autoload.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Autoload extends Singleton
{
    /**
     * Root dir
     *
     * @var string
     */
    protected $rootDir;

    /**
     * constructor
     *
     * @param void
     * @return this
     */
    protected function __construct() {
        $this->rootDir = Capsule::getInstance()->lib;
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Автозагрузчик
     *
     * @param string $classname
     * @throws Exception
     * @return boolean
     */
    public function autoload($classname) {
        if ($this->load($classname)) {
            if (class_exists($classname, false)) {
                return true;
            }
            if (interface_exists($classname, false)) {
                return true;
            }
            if (trait_exists($classname, false)) {
                return true;
            }
        }
        $msg = 'Class ' . $classname . ' not found';
        #throw new Exception($msg);
    }

    /**
     * Загружает класс
     *
     * @param string $classname
     * @return boolean
     */
    protected function load($classname) {
        $path = $this->normalizePath($this->rootDir . '/' . $classname . '.php');
        if (file_exists($path)) {
            include $path;
            return true;
        }
        return false;
    }

    /**
     * Normalize path
     *
     * @param string $path
     * @return string
     */
    private function normalizePath($path) {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', trim($path))), '/');
    }
}