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
     * Autoload constructor.
     */
    protected function __construct()
    {
        $this->rootDir = Capsule::getInstance()->systemRoot . '/' . Capsule::DIR_SRC;
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Автозагрузчик
     *
     * @param string $class_name
     * @return boolean
     */
    public function autoload($class_name)
    {
        if ($this->load($class_name)) {
            if (class_exists($class_name, false)) {
                return true;
            }
            if (interface_exists($class_name, false)) {
                return true;
            }
            if (trait_exists($class_name, false)) {
                return true;
            }
        }
    }

    /**
     * Загружает класс
     *
     * @param string $class_name
     * @return boolean
     */
    protected function load($class_name)
    {
        $path = $this->normalizePath($this->rootDir . '/' . $class_name . '.php');
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
    private function normalizePath($path)
    {
        return rtrim(preg_replace('|/{2,}|', '/', str_replace('\\', '/', trim($path))), '/');
    }
}