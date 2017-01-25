<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 26.07.2014 18:23:14 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Plugin\Storage\Driver;

use Capsule\Component\Config\Config;
use Capsule\Component\Path\Path;
use PHP\Exceptionizer\Exceptionizer;
use Capsule\Capsule;

/**
 * Local.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Local extends Driver
{
    /**
     * Каталог для файлов
     *
     * @var Path
     */
    protected $files;

    /**
     * @param Config $config
     * @throws \Exception
     * @return self
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->files = new Path($this->config->files);
        $this->files->absolutize();
        if (!$this->files->isDir()) {
            $msg = 'Not found files dir: ' . $this->files;
            throw new \Exception($msg);
        }
    }

    /**
     * @param void
     * @return Path
     */
    public function path()
    {
        return $this->files;
    }

    /**
     * (non-PHPdoc)
     * @see \Capsule\Plugin\Storage\Driver\IDriver::addFile()
     */
    public function addFile($source_absolute_path, $extension = null)
    {
        $e = new Exceptionizer;
        // check file exists
        $path = new Path($source_absolute_path);
        if (!$path->fileExists()) {
            $msg = 'Source file not exists: ' . $source_absolute_path;
            throw new \Exception($msg);
        }
        // calculate file param
        $hash = md5_file($path->toString());
        $file_relative_dir = '/' . join('/', array_slice(str_split($hash, 3), 0, 3));
        $file_absolute_dir = new Path($this->files, $file_relative_dir);
        // check file dir
        if (!$file_absolute_dir->isDir()) {
            mkdir($file_absolute_dir->toString(), 0755, true);
        }
        if (!$file_absolute_dir->isDir()) {
            $msg = 'Unable to create directory: ' . $file_absolute_dir;
            throw new \Exception($msg);
        }
        // handle extension
        if (is_null($extension)) {
            $extension = $path->extension();
        } else {
            settype($extension, 'string');
        }
        if (strlen($extension)) $extension = '.' . strtolower($extension);
        $file_relative_path = new Path($file_relative_dir, $hash . $extension);
        $file_absolute_path = new Path($this->files, $file_relative_path);
        $file_exists = false;
        if ($file_absolute_path->fileExists()) {
            // trying to change existing file mode (наверное не нужно это)
            if (!chmod($file_absolute_path->toString(), 0644)) {
                $msg = 'Unable to changes file mode';
                throw new \Exception($msg);
            }
            $file_exists = true;
        } else {
            // trying to copy file
            copy($path->toString(), $file_absolute_path->toString());
            if (!$file_absolute_path->fileExists()) {
                $msg = 'Unable to copy file';
                throw new \Exception($msg);
            }
            // trying to change file mode
            if (!chmod($file_absolute_path->toString(), 0644)) {
                $msg = 'Unable to changes file mode';
                throw new \Exception($msg);
            }
        }
        return array(
            'pathname' => $file_absolute_path->toString(),
            'exists' => $file_exists,
            'url' => '/' . $file_absolute_path->substract(Capsule::getInstance()->documentRoot)->toString()
        );
    }

    public function delFile($filename)
    {
        $e = new Exceptionizer;
        // calculate file param
        $file_relative_dir = '/' . join('/', array_slice(str_split($filename, 3), 0, 3));
        $file_absolute_dir = new Path($this->files, $file_relative_dir);
        $file_relative_path = new Path($file_relative_dir, $filename);
        $file_absolute_path = new Path($this->files, $file_relative_path);
        if (file_exists($file_absolute_path)) {
            unlink($file_absolute_path);
        } else {
            $msg = 'File not found: ' . $filename;
            throw new \Exception($msg);
        }
        if (file_exists($file_absolute_path)) {
            $msg = 'Unable to delete file: ' . $filename;
            throw new \Exception($msg);
        }
        return true;
    }
}