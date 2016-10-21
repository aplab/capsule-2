<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 22.10.2016
 * Time: 0:30
 */

namespace Capsule\Component\Path;


use Capsule\Capsule;

class ComponentConfigPath extends Path
{
    /**
     * @var string
     */
    const DEFAULT_EXTENSION = 'json';

    /**
     * ComponentConfigPath constructor.
     * @param $class
     */
    public function __construct($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        parent::__construct(
            Capsule::getInstance()->systemRoot,
            Capsule::DIR_CONFIG,
            $class . '.' . ltrim(static::DEFAULT_EXTENSION, '.')
        );
    }

    /**
     * Create this file if not exists
     *
     * @return string
     * @throws Exception
     */
    public function createFile()
    {
        if ($this->fileExists()) {
            throw new Exception('File exists');
        }
        $path = $this->toString();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            $is_dir = mkdir($dir, 0755, true);
            if (!$is_dir || !is_dir($dir)) {
                $msg = 'Unable to create directory: ' . $dir;
                throw new Exception($msg);
            }
        }
        if (false === touch($path)) {
            $msg = 'Unable to create file: ' . $path;
            throw new Exception($msg);
        }
        return $path;
    }
}